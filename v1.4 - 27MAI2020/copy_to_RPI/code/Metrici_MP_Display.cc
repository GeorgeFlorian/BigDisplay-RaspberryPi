#include "led-matrix.h"
#include "graphics.h"

#include <iostream>
#include <chrono>
#include <ctime>
#include <iomanip>
#include <sstream>
#include <string>
#include <unistd.h>
#include <fstream>
#include <sys/file.h>
#include <ext/stdio_filebuf.h>
#include <algorithm>
#include <curl/curl.h>

using namespace rgb_matrix;

//Pre-load the fonts
const char *_10_20_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/10x20.bdf";
const char *ArialB_15_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_15.bdf";
const char *ArialB_20_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_20.bdf";
const char *ArialB_22_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_22.bdf";

rgb_matrix::Font font_10_20;
rgb_matrix::Font font_Arial15;
rgb_matrix::Font font_Arial20;
rgb_matrix::Font font_Arial22;

rgb_matrix::Color RED(255,0,0);
rgb_matrix::Color ORANGE(255,128,0);
rgb_matrix::Color YELLOW(255, 255, 0);
rgb_matrix::Color GREEN(0, 255, 0);
rgb_matrix::Color BLUE(0,0,255);
rgb_matrix::Color INDIGO(75,0,130);
rgb_matrix::Color VIOLET(238, 130, 238);

rgb_matrix::Color WHITE(255, 255, 255);
rgb_matrix::Color BLACK(0, 0, 0);

static void text_on_one_line(RGBMatrix *canvas, std::string& line1,
                             int brightness = 50, rgb_matrix::Color color1 = WHITE) {
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial22, 0, font_Arial22.baseline()+10, color1, &BLACK, line1.c_str());
}

static void text_on_two_lines(RGBMatrix *canvas, std::string& line1, std::string& line2,
                              int brightness = 50, rgb_matrix::Color color1 = WHITE, rgb_matrix::Color color2 = WHITE) {
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial20, 0, font_Arial20.baseline()-7, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_Arial20, 0, font_Arial20.baseline()*2-5, color2, &BLACK, line2.c_str());
}

static void text_on_three_lines(RGBMatrix *canvas, std::string& line1, std::string& line2, std::string& line3,
                                int brightness = 50, rgb_matrix::Color color1 = WHITE, rgb_matrix::Color color2 = WHITE, rgb_matrix::Color color3 = WHITE) {
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial15, 0, font_Arial15.baseline()-5, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_Arial15, 0, font_Arial15.baseline()*2-5, color2, &BLACK, line2.c_str());
  rgb_matrix::DrawText(canvas, font_Arial15, 0, font_Arial15.baseline()*3-4, color3, &BLACK, line3.c_str());
}

static void error_on_three_lines(RGBMatrix *canvas, std::string& line1, std::string& line2, std::string& line3,
                                int brightness = 50, rgb_matrix::Color color1 = RED, rgb_matrix::Color color2 = WHITE, rgb_matrix::Color color3 = WHITE) {
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_10_20, 30, font_10_20.height()-2, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_10_20, 0, font_10_20.height()*2-2, color2, &BLACK, line2.c_str(),-1);
  rgb_matrix::DrawText(canvas, font_10_20, 0, font_10_20.height()*3-2, color3, &BLACK, line3.c_str(),-1);
}

// std::string time_in_HH_MM_SS_MMM()
// {
//     using namespace std::chrono;

//     // get current time
//     auto now = system_clock::now();

//     // get number of milliseconds for the current second
//     // (remainder after division into seconds)
//     auto ms = duration_cast<milliseconds>(now.time_since_epoch()) % 1000;

//     // convert to std::time_t in order to convert to std::tm (broken time)
//     auto timer = system_clock::to_time_t(now);

//     // convert to broken time
//     std::tm bt = *std::localtime(&timer);

//     std::ostringstream oss;

//     oss << std::put_time(&bt, "%H:%M:%S"); // HH:MM:SS
//     oss << '.' << std::setfill('0') << std::setw(3) << ms.count();

//     return oss.str();
// }

std::string strlog;

struct ring_buffer
{
  ring_buffer(size_t cap) : buffer(cap) {}

  bool empty() const { return sz == 0 ; }
  bool full() const { return sz == buffer.size() ; }

  void push( std::string str )
  {
      if(last >= buffer.size()) last = 0 ;
      buffer[last] = str ;
      ++last ;
      if(full()) 
    first = (first+1) %  buffer.size() ;
      else ++sz ;
  }

void print() const {
  strlog= "";
  // std::string current_time = "";
  if( first < last ) {
    for( size_t i = first ; i < last ; ++i ) {
      // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
      // strlog += (current_time + ": " + buffer[i] + "<br>");
      strlog += (buffer[i] + "<br>");
    }
  }else {
    for( size_t i = first ; i < buffer.size() ; ++i ) {
      // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
      // strlog += (current_time + ": " + buffer[i] + "<br>");
      strlog += (buffer[i] + "<br>");
    }
    for( size_t i = 0 ; i < last ; ++i ) {
      // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
      // strlog += (current_time + ": " + buffer[i] + "<br>");
      strlog += (buffer[i] + "<br>");
    }
  }
}

  private:
      std::vector<std::string> buffer ;
      size_t first = 0 ;
      size_t last = 0 ;
      size_t sz = 0 ;
};
//------------------------- struct circular_buffer
ring_buffer circle(10);

void log(std::string str) {
  circle.push(str);
  std::cout<<str<<std::endl;
}

void readFromFile(std::fstream& configFile, std::string (&str)[10], std::string identity) {
  // Get content line by line of txt file
  int i = 0;
  while(getline(configFile, str[i++]));

  if((i-1) == 0) {
    std::cout<<identity<<": "<<str[i-1]<<std::endl;
  } else {
      for(int k = 0; k<i-1; k++) {
        std::cout<<identity<<": "<<str[k]<<std::endl;
      }
  }
}

// trim from start (in place)
static inline void ltrim(std::string &s) {
    s.erase(s.begin(), std::find_if(s.begin(), s.end(), [](int ch) {
        return !std::isspace(ch);
    }));
}

// trim from end (in place)
static inline void rtrim(std::string &s) {
    s.erase(std::find_if(s.rbegin(), s.rend(), [](int ch) {
        return !std::isspace(ch);
    }).base(), s.end());
}

// trim from both ends (in place)
static inline void trim(std::string &s) {
    ltrim(s);
    rtrim(s);
}

int main(int argc, char *argv[]) {

  // std::cout<<"argument count: "<<argc<<std::endl;
  if(argc == 2) {
    std::string argument = argv[1];
    if(argument.compare("-v") == 0) {
      system("echo 'Metrici_128x64_Display v1.4'");
    }
  } else {
    RGBMatrix::Options defaults;
    RuntimeOptions runtime_defaults;
    defaults.hardware_mapping = "regular";  // or e.g. "adafruit-hat"
    defaults.multiplexing = 1;
    defaults.cols = 64;
    defaults.rows = 32;
    defaults.chain_length = 2;
    defaults.parallel = 2;
    //defaults.pixel_mapper_config = "U-mapper";
    defaults.disable_hardware_pulsing = true;
    defaults.show_refresh_rate = false;
    runtime_defaults.gpio_slowdown = 4;
    runtime_defaults.drop_privileges = 0;
    RGBMatrix *canvas = rgb_matrix::CreateMatrixFromFlags(&argc, &argv, &defaults, &runtime_defaults);
    if (canvas == NULL)  return 1;

    std::cout<<"\nLoading fonts...\n";
    
    if(!font_10_20.LoadFont(_10_20_path)) {
        std::cout<<"Couldn't load font font_10_20\n";
    } else {
      std::cout<<"font_10_20 loaded !\n";
    }
    
    if(!font_Arial15.LoadFont(ArialB_15_path)) {
        std::cout<<"Couldn't load font ArialB_15\n";
    } else {
      std::cout<<"ArialB_15 loaded !\n";
    }
    
    if(!font_Arial20.LoadFont(ArialB_20_path)) {
        std::cout<<"Couldn't load font ArialB_20\n";
    } else {
      std::cout<<"ArialB_20 loaded !\n";
    }

    if(!font_Arial22.LoadFont(ArialB_22_path)) {
      std::cout<<"Couldn't load font ArialB_22\n";
    } else {
      std::cout<<"ArialB_22 loaded !\n";
    }

    char urlResponsePath[]="/var/www/html/code/url_response.txt";
    std::fstream urlResponseFile;
    char configFilePath[]="/var/www/html/website/config/configuration.txt";
    std::fstream configFile;
    char dhcpcd_txtFilePath[]="/var/www/html/website/config/dhcpcd_conf.txt";
    std::fstream dhcpcd_txtFile;
    char wpa_txtFilePath[]="/var/www/html/website/config/wpa_conf.txt";
    std::fstream wpa_txtFile;
    char dhcpcd_confFilePath[]="/etc/dhcpcd.conf";
    std::fstream dhcpcd_confFile;
    char wpa_confFilePath[]="/etc/wpa_supplicant/wpa_supplicant.conf";
    std::fstream wpa_confFile;
    char logFilePath[]="/var/www/html/website/config/log_file.txt";
    std::fstream logFile;

    char config_lockFilePath[]="/var/www/html/website/config/configuration.lock";
    int config_lockFile;
    char dhcpcd_lockFilePath[]="/var/www/html/website/config/dhcpcd_conf.lock";
    int dhcpcd_lockFile;
    char wpa_lockFilePath[]="/var/www/html/website/config/wpa_conf.lock";
    int wpa_lockFile;

    std::string current_display_settings[3] = {"URL not entered", "2", "50"};
    std::string url_response = "URL not entered";
    std::string current_dhcpcd_conf_content[10];
    std::string current_wpa_conf_content[10];
    bool shouldChangePanel1 = true;
    bool shouldChangePanel2 = true;
    int nr_of_lines = 0;
    rgb_matrix::Color color[10];
    std::string lines[10];
    int val_system = 0;
    int is_error = 0;
    int error_counter = 0;
    int refresh_interval = 2;
    int brightness = 50;
    std::string nr_of_lines_string = "";
    bool dhcpcd_conf_flag = false;
    bool wpa_conf_flag = false;

    while(!dhcpcd_conf_flag && !wpa_conf_flag) {
      dhcpcd_confFile.open(dhcpcd_confFilePath, std::fstream::in);
      if(dhcpcd_confFile.is_open()) {
        dhcpcd_conf_flag = true;
        std::cout<<"Successfully opened dhcpcd_conf file to read."<<std::endl;
        readFromFile(dhcpcd_confFile, current_dhcpcd_conf_content, "current_dhcpcd_conf");
        dhcpcd_confFile.close();
      } else {
        dhcpcd_conf_flag = false;
        std::cout<<"Could not open dhcpcd_conf file to read."<<std::endl;
      }

      wpa_confFile.open(wpa_confFilePath, std::fstream::in);
      if(wpa_confFile.is_open()) {
        wpa_conf_flag = true;
        std::cout<<"Successfully opened wpa_conf file to read."<<std::endl;
        readFromFile(wpa_confFile, current_wpa_conf_content, "current_wpa_conf");
        wpa_confFile.close();
      } else {
        wpa_conf_flag = false;
        std::cout<<"Could not open wpa_conf file to read."<<std::endl;
      }
    }

    if(dhcpcd_confFile.is_open()) { dhcpcd_confFile.close(); }
    if(wpa_confFile.is_open()) { wpa_confFile.close(); }

    while(true) {
      std::cout<<"--------------------------------------------------------------------"<<std::endl;
      // refresh URL Display Text
      val_system = system("/var/www/html/code/get_page.sh");
      std::cout<<"System return value: "<<WEXITSTATUS(val_system)<<std::endl;
      // std::cout<<"After system"<<std::endl;
      if (WEXITSTATUS(val_system) == 0) {
        log("Connection established !");
        is_error = 0;
      }

      config_lockFile = open(config_lockFilePath, O_WRONLY | O_CREAT);
      if(flock(config_lockFile, LOCK_SH) == 0) {
        configFile.open(configFilePath, std::fstream::in);
        // std::cout<<"After configFile.open"<<std::endl;
        if(configFile.is_open()){
          std::cout<<"Successfully opened Display Configuration file to read."<<std::endl;
          std::string new_display_settings[10];
          std::string id = "display_settings";
          readFromFile(configFile, new_display_settings, id);
          configFile.close();
          // std::cout<<"After configFile.close()"<<std::endl;
          //check if any config has changed before refreshing panel      
          if(new_display_settings[0]!= current_display_settings[0] ||
            new_display_settings[1]!= current_display_settings[1] ||
            new_display_settings[2]!= current_display_settings[2])
          {
            // std::cout<<"Inside configFile if()"<<std::endl;
            current_display_settings[0] = new_display_settings[0];
            current_display_settings[1] = new_display_settings[1];
            current_display_settings[2] = new_display_settings[2];
            log("Entered URL: " + current_display_settings[0]);
            log("Entered URL Refresh Rate: " + current_display_settings[1]);          
            log("Entered Brightness: " + current_display_settings[2]);
            if (sscanf(current_display_settings[1].c_str(), "%d", &refresh_interval) != 1) {
              refresh_interval = 2;
              current_display_settings[1] = "2";
              log("Something went wrong ! Please enter again the URL Refresh Rate.");
            }
            if (sscanf(current_display_settings[2].c_str(), "%d", &brightness) != 1) {
              brightness = 50;
              current_display_settings[2] = "50";
              log("Something went wrong ! Please enter again the Brightness value.");
            }
            shouldChangePanel1 = true;
          } else {
              // std::cout<<"Inside configFile else()"<<std::endl;
              shouldChangePanel1 = false;
          }
        } else {
          log("Error ! Could not open Display Configuration file to read");
        }
        flock(config_lockFile, LOCK_UN);
      } else {
        std::cout<<"Could not lock configuration.lock"<<std::endl;
      }
      close(config_lockFile);
      
      std::cout<<std::endl;

      urlResponseFile.open(urlResponsePath, std::fstream::in);
      // std::cout<<"After urlFile open"<<std::endl;
      if(urlResponseFile.is_open()) {
        std::cout<<"Successfully opened URL Display Text file to read."<<std::endl;
        std::string inputs[10];
        std::string id = "url_text";
        readFromFile(urlResponseFile, inputs, id);
        urlResponseFile.close();
        // std::cout<<"After urlFile close"<<std::endl;
        log("Display Text: " + inputs[0]);
        //check if parking places have changed
        if(inputs[0] != url_response) {
          // std::cout<<"Inside first urlFile if()"<<std::endl;
          url_response = inputs[0];
          //check if string begins with #BEGIN
          if(url_response.substr(0,6)=="#BEGIN") {
            // std::cout<<"Inside second urlFile if()"<<std::endl;
            std::string copy = url_response;
            copy.replace(0,6,"");
            trim(copy);        
            //#GLiber 11#ROcupat 39#YTotal 50
            std::istringstream url_stream(copy);
            //reset the lines to be rewritable
            for(int i=0; i<5; i++){
              lines[i] = "";
            }
            nr_of_lines = 0;
            while(getline(url_stream, lines[nr_of_lines++], '#'));
            // std::cout<<"nr_of_lines= "<<nr_of_lines<<std::endl;
            std::string line_color = "";
            // char color;
            for(int i=1; i<nr_of_lines-1; i++) {
              line_color = "";
              if(lines[i].substr(0,1)=="R") {
                color[i-1]=RED;
                line_color = "Red";
              } else if(lines[i].substr(0,1)=="O") {
                color[i-1]=ORANGE;
                line_color = "Orange";
              } else if(lines[i].substr(0,1)=="Y") {
                color[i-1]=YELLOW;
                line_color = "Yellow";
              } else if(lines[i].substr(0,1)=="G") {
                color[i-1]=GREEN;
                line_color = "Green";
              } else if(lines[i].substr(0,1)=="B") {
                color[i-1]=BLUE;
                line_color = "Blue";
              } else if(lines[i].substr(0,1)=="I") {
                color[i-1]=INDIGO;
                line_color = "Indigo";
              } else if(lines[i].substr(0,1)=="V") {
                color[i-1]=VIOLET;
                line_color = "Violet";
              } else if(lines[i].substr(0,1)=="W") {
                color[i-1]=WHITE;
                line_color = "White";
              } else {
                color[i-1]=RED;
                line_color = "Invalid";
              }
              lines[i].replace(0,1,"");
              if(line_color == "Invalid") {
                lines[i] = "Color Invalid";
                line_color = "Invalid Color";
              }
              log("Line " + std::to_string(i) + ": " + lines[i]);
              log("Color for line " + std::to_string(i) + ": " + line_color);
            }
            shouldChangePanel2 = true;          
          } else if(WEXITSTATUS(val_system) == 4) {
            shouldChangePanel2 = false;          
            is_error = 1;
          } else {
            // std::cout<<"Inside urlFile third else()"<<std::endl;
            nr_of_lines = 2;
            shouldChangePanel1 = false;
            shouldChangePanel2 = false;
            if(WEXITSTATUS(val_system) == 0) {
              is_error = 2;
            } else {
              is_error = 3;
            }
          } //if(inputs[0].substr(0,6)=="#BEGIN")
        } else {
          // parking places have not changed
          // std::cout<<"parking places have not changed"<<std::endl;
          shouldChangePanel2 = false;
        } //if(inputs[0] != url_response)      

        if(shouldChangePanel1) {
          std::cout<<"shouldChangePanel 1 is true"<<std::endl;
        }
        if(shouldChangePanel2) {
          std::cout<<"shouldChangePanel 2 is true"<<std::endl;
        }
        //parse the url response and display accordingly
        if(shouldChangePanel1 || shouldChangePanel2) {
          // std::cout<<"Inside shouldChangePanel if()"<<std::endl;
          error_counter = 0;
          switch ((nr_of_lines-2))
          {
            case 1:
            {
              std::cout<<"Case 1"<<std::endl;
              text_on_one_line(canvas, lines[1], brightness, color[0]);
              break;
            }
            case 2:
            {
              std::cout<<"Case 2"<<std::endl;
              text_on_two_lines(canvas, lines[1], lines[2], brightness, color[0], color[1]);
              break;
            }
            case 3:
            {
              std::cout<<"Case 3"<<std::endl;
              text_on_three_lines(canvas, lines[1], lines[2], lines[3], brightness, color[0], color[1], color[2]);          
              break;
            }
            default:
            { 
              is_error = 4;
              break;
            }
          } // switch()
        } // if(shouldChangePanel1 || shouldChangePanel2)
      } else {
        // Could not open urlResponseFile
        log("Error ! Could not open URL Display Text file to read.");
      }
      
      if((nr_of_lines-2) > 3 || (nr_of_lines-2) == 0){
        is_error = 4;
      }
      if(WEXITSTATUS(val_system) == 8) {
        is_error = 3;
      }

      nr_of_lines_string = std::to_string((nr_of_lines-2));
      std::cout<<"is_error: "<<is_error<<std::endl;

      if(is_error == 0) {
        log("Number of lines: " + nr_of_lines_string);
      } else if(is_error == 1) {
        log("Warning ! Connection timeout.");
      } else if(is_error == 2) {
        error_counter++;
        log("ERROR ! Check URL or check URL Display Text !");
      } else if(is_error == 3) {
        error_counter++;
        log("ERROR Code: " + std::to_string(WEXITSTATUS(val_system)));
        log("Check URL or check URL Display Text");      
      } else if(is_error == 4) {
        error_counter++;
        log("Number of lines: " + nr_of_lines_string);
        log("ERROR ! Check URL Display Text. Maximum allowed number of lines is 3.");
      }

      std::cout<<"error_counter = "<<error_counter<<std::endl;
      if(error_counter > 2) {
        if(is_error == 2 || is_error == 3) {
          error_counter = 0;
          std::string Met = "Metrici";
          std::string error1 = "Check URL";
          std::string error2 = "Check URL text";
          error_on_three_lines(canvas, Met, error1, error2, brightness);
        } else if (is_error == 4) {
          error_counter = 0;
          std::string Met = "Metrici";
          std::string error1 = "Invalid no of";
          std::string error2 = "lines in URL";
          error_on_three_lines(canvas, Met, error1, error2, brightness);
        }
      }
        
      std::cout<<std::endl;

      bool success_dhcpcd = false;
      bool success_wpa = false;
      bool should_change_dhcpcd = false;
      bool should_change_wpa = false;

      dhcpcd_lockFile = open(dhcpcd_lockFilePath, O_WRONLY | O_CREAT);
      if(flock(dhcpcd_lockFile, LOCK_SH) == 0) {
        dhcpcd_txtFile.open(dhcpcd_txtFilePath, std::fstream::in);
        if(dhcpcd_txtFile.is_open()) {
          std::cout<<"Successfully opened dhcpcd_txt file to read"<<std::endl;
          std::string new_dhcpcd_txt_content[10];
          readFromFile(dhcpcd_txtFile, new_dhcpcd_txt_content, "new_dhcpcd_txt");
          dhcpcd_txtFile.close();

          int k = 0;
          while(new_dhcpcd_txt_content[k].length()!=0 && k<9) {
            k++;
          }
          std::cout<<"dhcpcd k= "<<k<<std::endl;

          for(int i=0; i<9;i++) {
            if(current_dhcpcd_conf_content[i]!=new_dhcpcd_txt_content[i]) {
              should_change_dhcpcd = true;
              current_dhcpcd_conf_content[i]=new_dhcpcd_txt_content[i];
            }
          }
          
          if(should_change_dhcpcd) {
            dhcpcd_confFile.open(dhcpcd_confFilePath, std::fstream::out | std::fstream::trunc);
            if(dhcpcd_confFile.is_open()) {
              success_dhcpcd = true;
              std::cout<<"Successfully opened dhcpcd_conf file to write."<<std::endl;
              for(int i = 0; i<k; i++) {
                dhcpcd_confFile<<current_dhcpcd_conf_content[i]<<std::endl;
              }
              dhcpcd_confFile.close();
            } else {
              current_dhcpcd_conf_content[0] = "";
              std::cout<<"Could not open dhcpcd_conf file to write."<<std::endl;
            }
          }

        } else {
          std::cout<<"Could not open dhcpcd_txt file to read."<<std::endl;
        }
        flock(dhcpcd_lockFile, LOCK_UN);
      } else {
        std::cout<<"Could not lock dhcpcd lock file."<<std::endl;
      }
      close(dhcpcd_lockFile);

      wpa_lockFile = open(wpa_lockFilePath, O_WRONLY | O_CREAT);
      if(flock(wpa_lockFile, LOCK_SH) == 0) {
          wpa_txtFile.open(wpa_txtFilePath, std::fstream::in);
          if(wpa_txtFile.is_open()) {
            std::cout<<"Successfully opened wpa_txt file to read."<<std::endl;
            std::string new_wpa_txt_content[10];
            readFromFile(wpa_txtFile, new_wpa_txt_content, "new_wpa_txt");
            wpa_txtFile.close();

            int k = 0;
            while(new_wpa_txt_content[k].length()!=0 && k<9) {
              k++;
            }
            std::cout<<"wpa k= "<<k<<std::endl;

            for(int i=0; i<9;i++) {
              if(current_wpa_conf_content[i]!=new_wpa_txt_content[i]) {
                should_change_wpa = true;
                current_wpa_conf_content[i]=new_wpa_txt_content[i];
              }
            }
            
            if(should_change_wpa) {
              wpa_confFile.open(wpa_confFilePath, std::fstream::out | std::fstream::trunc);
              if(wpa_confFile.is_open()) {
                success_wpa = true;
                std::cout<<"Successfully opened wpa_conf file to write."<<std::endl;
                for(int i = 0; i<k; i++) {
                  wpa_confFile<<current_wpa_conf_content[i]<<std::endl;
                }
                wpa_confFile.close();
              } else {
                current_wpa_conf_content[0]="";
                std::cout<<"Could not open wpa_conf file to write."<<std::endl;
              }
            }

          } else {
            std::cout<<"Could not open wpa_txt file to read."<<std::endl;
          }
          flock(wpa_lockFile, LOCK_UN);
      } else {
        std::cout<<"Could not lock wpa lock file."<<std::endl;
      }
      close(wpa_lockFile);    

      if(success_wpa || success_dhcpcd) {
        log("SUCCESS ! Network Settings applied !");
        system("ip link set eth0 down && sleep 1");
        system("ip link set eth0 up && sleep 1");
        system("ip link set wlan0 down && sleep 1");
        system("ip link set wlan0 up && sleep 1");
        system("systemctl daemon-reload && sleep 1");
        system("systemctl restart dhcpcd.service && sleep 1");
      } else if(should_change_wpa || should_change_dhcpcd){
        log("ERROR ! Could not apply Network Settings !");
      }

      logFile.open(logFilePath, std::fstream::out | std::fstream::trunc);
      // std::cout<<"After logFile open"<<std::endl;
      if(logFile.is_open()) {
        std::cout<<"Successfully opened Log file"<<std::endl;
        circle.print();
        logFile<<strlog;
        logFile.close();
        // std::cout<<"After logFile close"<<std::endl;
      }
      usleep(refresh_interval*1000*999);
      // std::cout<<"After usleep. End of while."<<std::endl;
      std::cout<<"--------------------------------------------------------------------"<<std::endl;
    }

    canvas->Clear();
    delete canvas;
  }

  return 0;
}
