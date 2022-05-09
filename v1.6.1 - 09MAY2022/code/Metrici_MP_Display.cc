#include "led-matrix.h"
#include "graphics.h"

#include <iostream>
#include <sstream>
#include <string>
#include <unistd.h>
#include <fstream>
#include <sys/file.h>
#include <ext/stdio_filebuf.h>
#include <algorithm>

// #include <chrono>
// #include <ctime>
// #include <iomanip>
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

using namespace rgb_matrix;

// Pre-load the fonts
const char *_10_20_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/10x20.bdf";
const char *ArialB_15_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_15.bdf";
const char *ArialB_20_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_20.bdf";
const char *ArialB_22_path = "/var/www/html/code/rpi-rgb-led-matrix/fonts/ArialB_22.bdf";

const char *Arrows_38_path = "/var/www/html/website/config/Arrows_38.bdf";
const char *Arrows_44_path = "/var/www/html/website/config/Arrows_44.bdf";

rgb_matrix::Font font_10_20;
rgb_matrix::Font font_Arial15;
rgb_matrix::Font font_Arial20;
rgb_matrix::Font font_Arial22;

rgb_matrix::Font font_Arrows_38;
rgb_matrix::Font font_Arrows_44;

void loadFonts()
{
  std::cout << "\nLoading fonts...\n";

  if (!font_10_20.LoadFont(_10_20_path))
  {
    std::cout << "Couldn't load font font_10_20\n";
  }
  else
  {
    std::cout << "font_10_20 loaded !\n";
  }

  if (!font_Arial15.LoadFont(ArialB_15_path))
  {
    std::cout << "Couldn't load font ArialB_15\n";
  }
  else
  {
    std::cout << "ArialB_15 loaded !\n";
  }

  if (!font_Arial20.LoadFont(ArialB_20_path))
  {
    std::cout << "Couldn't load font ArialB_20\n";
  }
  else
  {
    std::cout << "ArialB_20 loaded !\n";
  }

  if (!font_Arial22.LoadFont(ArialB_22_path))
  {
    std::cout << "Couldn't load font ArialB_22\n";
  }
  else
  {
    std::cout << "ArialB_22 loaded !\n";
  }

  // new code: v1.5.1
  // if(!font_Arrows_38.LoadFont(Arrows_38_path)) {
  //   std::cout<<"Couldn't load font Arrows_38\n";
  // } else {
  //   std::cout<<"Arrows_38 loaded !\n";
  // }

  // if(!font_Arrows_44.LoadFont(Arrows_44_path)) {
  //   std::cout<<"Couldn't load font Arrows_44\n";
  // } else {
  //   std::cout<<"Arrows_44 loaded !\n";
  // }
  // end code: v1.5.1
}

rgb_matrix::Color RED(255, 0, 0);
rgb_matrix::Color ORANGE(255, 128, 0);
rgb_matrix::Color YELLOW(255, 255, 0);
rgb_matrix::Color GREEN(0, 255, 0);
rgb_matrix::Color BLUE(0, 0, 255);
rgb_matrix::Color SKYBLUE(0, 191, 255);
rgb_matrix::Color INDIGO(75, 0, 130);
rgb_matrix::Color VIOLET(238, 130, 238);

rgb_matrix::Color WHITE(255, 255, 255);
rgb_matrix::Color BLACK(0, 0, 0);

std::string strlog;

struct ring_buffer
{
  ring_buffer(size_t cap) : buffer(cap) {}

  bool empty() const { return sz == 0; }
  bool full() const { return sz == buffer.size(); }

  void push(std::string str)
  {
    if (last >= buffer.size())
      last = 0;
    buffer[last] = str;
    ++last;
    if (full())
      first = (first + 1) % buffer.size();
    else
      ++sz;
  }

  void print() const
  {
    strlog = "";
    // std::string current_time = "";
    if (first < last)
    {
      for (size_t i = first; i < last; ++i)
      {
        // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
        // strlog += (current_time + ": " + buffer[i] + "<br>");
        strlog += (buffer[i] + "<br>");
      }
    }
    else
    {
      for (size_t i = first; i < buffer.size(); ++i)
      {
        // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
        // strlog += (current_time + ": " + buffer[i] + "<br>");
        strlog += (buffer[i] + "<br>");
      }
      for (size_t i = 0; i < last; ++i)
      {
        // current_time = "<span class='time-span'>[" + time_in_HH_MM_SS_MMM() + "]</span>";
        // strlog += (current_time + ": " + buffer[i] + "<br>");
        strlog += (buffer[i] + "<br>");
      }
    }
  }

private:
  std::vector<std::string> buffer;
  size_t first = 0;
  size_t last = 0;
  size_t sz = 0;
};
//------------------------- struct circular_buffer
ring_buffer circle(10);

std::string old_url_text = "URL not entered";
int nr_of_lines = 0;
rgb_matrix::Color color[10];
std::string lines[4];
int is_error = 0;
// Refresh display if there are new Display settings
bool refresh_display_1 = false;
// Refresh display if the output text has changed
bool refresh_display_2 = true;
int val_system = 0;

void log(std::string str)
{
  circle.push(str);
  std::cout << str << std::endl;
}

void text_on_one_line(RGBMatrix *canvas, std::string &line1,
                      int brightness = 50, rgb_matrix::Color color1 = WHITE)
{
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial22, 0, font_Arial22.baseline() + 10, color1, &BLACK, line1.c_str());
}

void text_on_two_lines(RGBMatrix *canvas, std::string &line1, std::string &line2,
                       int brightness = 50, rgb_matrix::Color color1 = WHITE, rgb_matrix::Color color2 = WHITE)
{
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial20, 0, font_Arial20.baseline() - 7, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_Arial20, 0, font_Arial20.baseline() * 2 - 5, color2, &BLACK, line2.c_str());
}

void text_on_three_lines(RGBMatrix *canvas, std::string &line1, std::string &line2, std::string &line3,
                         int brightness = 50, rgb_matrix::Color color1 = WHITE, rgb_matrix::Color color2 = WHITE, rgb_matrix::Color color3 = WHITE)
{
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_Arial15, 1, font_Arial15.baseline() - 5, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_Arial15, 1, font_Arial15.baseline() * 2 - 6, color2, &BLACK, line2.c_str());
  rgb_matrix::DrawText(canvas, font_Arial15, 1, font_Arial15.baseline() * 3 - 7, color3, &BLACK, line3.c_str());
}

void error_on_three_lines(RGBMatrix *canvas, std::string &line1, std::string &line2, std::string &line3,
                          int brightness = 50, rgb_matrix::Color color1 = RED, rgb_matrix::Color color2 = WHITE, rgb_matrix::Color color3 = WHITE)
{
  canvas->Clear();
  canvas->SetBrightness(brightness);

  rgb_matrix::DrawText(canvas, font_10_20, 30, font_10_20.height() - 2, color1, &BLACK, line1.c_str());
  rgb_matrix::DrawText(canvas, font_10_20, 0, font_10_20.height() * 2 - 2, color2, &BLACK, line2.c_str(), -1);
  rgb_matrix::DrawText(canvas, font_10_20, 0, font_10_20.height() * 3 - 2, color3, &BLACK, line3.c_str(), -1);
}

// new code: v1.5.1
// bool arrows = false;
// void display_arrows(RGBMatrix *canvas, std::string& line1, std::string& line2,
//                               int brightness = 50, rgb_matrix::Color color1 = WHITE, rgb_matrix::Color color2 = WHITE) {
//   canvas->Clear();
//   canvas->SetBrightness(brightness);

//   // up or down arrow
//   if(line1 == "c" || line1 == "h") {
//     int first_arrow = rgb_matrix::DrawText(canvas, font_Arrows_38, 1, font_Arrows_38.baseline()+5, color1, &BLACK, line1.c_str());
//     if(line2 == "c" || line2 == "h") {
//       rgb_matrix::DrawText(canvas, font_Arrows_38, first_arrow+23, font_Arrows_38.baseline()+5, color2, &BLACK, line2.c_str());
//     } else {
//       rgb_matrix::DrawText(canvas, font_Arrows_44, first_arrow+15, font_Arrows_44.baseline(), color2, &BLACK, line2.c_str());
//     }
//   }
//   // left or right arrow
//   else {
//     int first_arrow = rgb_matrix::DrawText(canvas, font_Arrows_44, 2, font_Arrows_44.baseline(), color1, &BLACK, line1.c_str());
//     if(line2 == "c" || line2 == "h") {
//       rgb_matrix::DrawText(canvas, font_Arrows_38, first_arrow+15, font_Arrows_38.baseline()+5, color2, &BLACK, line2.c_str());
//     } else {
//       rgb_matrix::DrawText(canvas, font_Arrows_44, first_arrow+7, font_Arrows_44.baseline(), color2, &BLACK, line2.c_str());
//     }
//   }
// }

// bool replace_str(std::string& str, const std::string& from, const std::string& to) {
//   size_t start_pos = str.find(from);
//   if(start_pos == std::string::npos)
//       return false;
//   // replace 'from' string with 'to' string
//   // str.replace(start_pos, from.length(), to);

//   // replace the whole string with arrow
//   str.replace(0, str.length(), to);
//   return true;
// }
// end code: v1.5.1

int readFromFile(std::fstream &settings_file, std::string (&str)[20], std::string identity)
{
  // Get content line by line of txt file
  int i = 0;
  while (!settings_file.eof())
  {
    while (getline(settings_file, str[i++]))
      ;
  }

  i--;

  for (int k = 0; k < i; k++)
  {
    std::cout << identity << ": " << str[k] << std::endl;
  }
  return i;
}

// trim from start (in place)
inline void ltrim(std::string &s)
{
  s.erase(s.begin(), std::find_if(s.begin(), s.end(), [](int ch)
                                  { return !std::isspace(ch); }));
}

// trim from end (in place)
inline void rtrim(std::string &s)
{
  s.erase(std::find_if(s.rbegin(), s.rend(), [](int ch)
                       { return !std::isspace(ch); })
              .base(),
          s.end());
}

// trim from both ends (in place)
inline void trim(std::string &s)
{
  ltrim(s);
  rtrim(s);
}

void getTextFromFile(char *file_path, std::string id)
{
  std::fstream file;
  file.open(file_path, std::fstream::in);

  if (file.is_open())
  {
    std::cout << "Successfully opened file to read Display Text." << std::endl;
    std::string new_url_text[20];
    readFromFile(file, new_url_text, id);
    file.close();

    log("Display Text: " + new_url_text[0]);
    // Check if the text has changed
    if (new_url_text[0] != old_url_text)
    {
      old_url_text = new_url_text[0];
      refresh_display_2 = true;
      // Delete #BEGIN from text
      if (new_url_text[0].substr(0, 6) == "#BEGIN")
      {
        new_url_text[0].replace(0, 6, "");
      }
      trim(new_url_text[0]);
      // Create a stream with the text
      std::istringstream url_stream(new_url_text[0]); // #GLiber 11#ROcupat 39#YTotal 50

      // Reset the number of lines
      nr_of_lines = 0;
      // Get lines and line number
      while (getline(url_stream, lines[nr_of_lines++], '#'))
        ;

      // std::cout << "nr_of_lines = " << nr_of_lines << std::endl; // 5

      std::string line_color = "";

      for (int i = 1; i < nr_of_lines - 1; i++)
      {
        if (lines[i].substr(0, 1) == "R")
        {
          color[i - 1] = RED;
          line_color = "Red";
        }
        else if (lines[i].substr(0, 1) == "O")
        {
          color[i - 1] = ORANGE;
          line_color = "Orange";
        }
        else if (lines[i].substr(0, 1) == "Y")
        {
          color[i - 1] = YELLOW;
          line_color = "Yellow";
        }
        else if (lines[i].substr(0, 1) == "G")
        {
          color[i - 1] = GREEN;
          line_color = "Green";
        }
        else if (lines[i].substr(0, 1) == "B")
        {
          color[i - 1] = BLUE;
          line_color = "Blue";
        }
        else if (lines[i].substr(0, 1) == "I")
        {
          color[i - 1] = INDIGO;
          line_color = "Indigo";
        }
        else if (lines[i].substr(0, 1) == "V")
        {
          color[i - 1] = VIOLET;
          line_color = "Violet";
        }
        else if (lines[i].substr(0, 1) == "W")
        {
          color[i - 1] = WHITE;
          line_color = "White";
        }
        else
        {
          color[i - 1] = RED;
          line_color = "Invalid";
        }
        // Delete color letter from text
        lines[i].replace(0, 1, "");

        if (line_color == "Invalid")
        {
          lines[i] = "ERROR !";
          line_color = "Invalid Color";
        }

        // new code: v1.5.1
        // if(replace_str(lines[i], "@up", "h")) arrows = true;
        // if(replace_str(lines[i], "@down", "c")) arrows = true;
        // if(replace_str(lines[i], "@left", "f")) arrows = true;
        // if(replace_str(lines[i], "@right", "g")) arrows = true;
        // end new code v1.5.1

        log("Line " + std::to_string(i) + " - " + lines[i] + " : " + line_color);
      }
    }
    else
    {
      // text did not change
      refresh_display_2 = false;
    }
  }
  else
  {
    log("Error ! Could not open file in order to read Display Text.");
  }
}

int main(int argc, char *argv[])
{

  // std::cout<<"argument count: "<<argc<<std::endl;
  if (argc == 2)
  {
    std::string argument = argv[1];
    if (argument.compare("-v") == 0)
    {
      system("echo 'Metrici_128x64_Display v1.6'");
    }
  }
  else
  {
    RGBMatrix::Options defaults;
    RuntimeOptions runtime_defaults;
    defaults.hardware_mapping = "regular"; // or e.g. "adafruit-hat"
    defaults.multiplexing = 1;
    defaults.cols = 64;
    defaults.rows = 32;
    defaults.chain_length = 2;
    defaults.parallel = 2;
    // defaults.pixel_mapper_config = "U-mapper";
    // defaults.led_rgb_sequence = "GRB";
    defaults.disable_hardware_pulsing = true;
    defaults.show_refresh_rate = false;
    runtime_defaults.gpio_slowdown = 4;
    runtime_defaults.drop_privileges = 0;
    RGBMatrix *canvas = rgb_matrix::CreateMatrixFromFlags(&argc, &argv, &defaults, &runtime_defaults);
    if (canvas == NULL)
      return 1;

    loadFonts();

    char url_response_path[] = "/var/www/html/code/url_response.txt";
    char settings_file_path[] = "/var/www/html/website/config/configuration.txt";
    std::fstream settings_file;
    char dhcpcd_txt_file_path[] = "/var/www/html/website/config/dhcpcd_conf.txt";
    std::fstream dhcpcd_txt_file;
    char wpa_txt_file_path[] = "/var/www/html/website/config/wpa_conf.txt";
    std::fstream wpa_txt_file;
    char dhcpcd_conf_path[] = "/etc/dhcpcd.conf";
    std::fstream dhcpcd_conf_file;
    char wpa_conf_path[] = "/etc/wpa_supplicant/wpa_supplicant.conf";
    std::fstream wpa_conf_file;
    char logs_file_path[] = "/var/www/html/website/config/log_file.txt";
    std::fstream logs_file;

    char settings_lock_path[] = "/var/www/html/website/config/configuration.lock";
    int settings_lock_file;
    char dhcpcd_lock_path[] = "/var/www/html/website/config/dhcpcd_conf.lock";
    int dhcpcd_lock_file;
    char wpa_lock_path[] = "/var/www/html/website/config/wpa_conf.lock";
    int wpa_lock_file;

    std::string current_display_settings[3] = {"URL not entered", "2", "50"};
    std::string current_dhcpcd_conf_content[20];
    std::string current_wpa_conf_content[20];
    int refresh_interval = 2;
    int brightness = 50;
    std::string nr_of_lines_string = "";
    bool dhcpcd_conf_flag = false;
    bool wpa_conf_flag = false;
    int current_dhcpcd_lines = 0;
    int current_wpa_lines = 0;

    // Read network configuration on start-up
    while (!dhcpcd_conf_flag && !wpa_conf_flag)
    {
      dhcpcd_conf_file.open(dhcpcd_conf_path, std::fstream::in);
      if (dhcpcd_conf_file.is_open())
      {
        dhcpcd_conf_flag = true;
        std::cout << "Successfully opened dhcpcd_conf file to read." << std::endl;
        current_dhcpcd_lines = readFromFile(dhcpcd_conf_file, current_dhcpcd_conf_content, "current_dhcpcd_conf");
        dhcpcd_conf_file.close();
      }
      else
      {
        dhcpcd_conf_flag = false;
        std::cout << "Could not open dhcpcd_conf file to read." << std::endl;
      }

      wpa_conf_file.open(wpa_conf_path, std::fstream::in);
      if (wpa_conf_file.is_open())
      {
        wpa_conf_flag = true;
        std::cout << "Successfully opened wpa_conf file to read." << std::endl;
        current_wpa_lines = readFromFile(wpa_conf_file, current_wpa_conf_content, "current_wpa_conf");
        wpa_conf_file.close();
      }
      else
      {
        wpa_conf_flag = false;
        std::cout << "Could not open wpa_conf file to read." << std::endl;
      }
    }

    if (dhcpcd_conf_file.is_open())
    {
      dhcpcd_conf_file.close();
    }
    if (wpa_conf_file.is_open())
    {
      wpa_conf_file.close();
    }

    // MAIN PROGRAM
    while (true)
    {
      std::cout << "--------------------------------------------------------------------" << std::endl;

      // Check for new Display settings
      settings_lock_file = open(settings_lock_path, O_WRONLY | O_CREAT);
      if (flock(settings_lock_file, LOCK_SH) == 0)
      {
        settings_file.open(settings_file_path, std::fstream::in);
        // std::cout<<"After settings_file.open"<<std::endl;

        if (settings_file.is_open())
        {
          std::cout << "Successfully opened Display Configuration file to read." << std::endl;
          std::string new_display_settings[20];
          std::string id = "display_settings";
          int k_lines = 0;
          k_lines = readFromFile(settings_file, new_display_settings, id);
          settings_file.close();
          // std::cout<<"After settings_file.close()"<<std::endl;
          // check if any config has changed before refreshing panel

          for (int i = 0; i < k_lines; i++)
          {
            if (new_display_settings[i] != current_display_settings[i])
            {
              current_display_settings[i] = new_display_settings[i];
              refresh_display_1 = true;
            }
          }

          if (refresh_display_1)
          {
            log("Entered URL: " + current_display_settings[0]);
            log("Entered URL Refresh Rate: " + current_display_settings[1]);
            log("Entered Brightness: " + current_display_settings[2]);
            if (sscanf(current_display_settings[1].c_str(), "%d", &refresh_interval) != 1)
            {
              refresh_interval = 2;
              current_display_settings[1] = "2";
              log("Something went wrong ! Please enter again the URL Refresh Rate.");
            }
            if (sscanf(current_display_settings[2].c_str(), "%d", &brightness) != 1)
            {
              brightness = 50;
              current_display_settings[2] = "50";
              log("Something went wrong ! Please enter again the Brightness value.");
            }
          }
        }
        else
        {
          log("Error ! Could not open Display Configuration file to read");
        }
        flock(settings_lock_file, LOCK_UN);
      }
      else
      {
        std::cout << "Could not lock configuration.lock" << std::endl;
      }
      close(settings_lock_file);

      // GET Display Text from server URL
      val_system = system("/var/www/html/code/get_text_from_url.sh");
      std::cout << "System return value: " << WEXITSTATUS(val_system) << std::endl;

      switch (WEXITSTATUS(val_system))
      {
      case 0:
        log("Connection established !");
        break;
      case 4:
        log("Warning ! Connection timeout !");
        // continue;
        break;
      case 8:
      {
        log("Warning ! Check URL !");
        std::string line1 = "Metrici";
        std::string line2 = "Check URL";
        std::string line3 = "Check URL text";
        error_on_three_lines(canvas, line1, line2, line3, brightness);
        continue;
      }
      default:
        break;
      }

      std::cout << std::endl;

      // Check for new text from url_response.txt
      getTextFromFile(url_response_path, "url_text");

      // Check for errors
      nr_of_lines_string = std::to_string((nr_of_lines - 2));
      if ((nr_of_lines - 2) > 3 || (nr_of_lines - 2) == 0)
      {
        log("Number of lines: " + nr_of_lines_string);
        log("ERROR ! Check URL Display Text. Maximum allowed number of lines is 3.");
        std::string line1 = "Metrici";
        std::string line2 = "Invalid no of";
        std::string line3 = "lines in URL";
        error_on_three_lines(canvas, line1, line2, line3, brightness);
        refresh_display_1 = false;
        refresh_display_2 = false;
      }

      std::cout << "refresh_display_1: " << refresh_display_1 << std::endl;
      std::cout << "refresh_display_2: " << refresh_display_2 << std::endl;
      // parse the url response and display accordingly
      if (refresh_display_1 || refresh_display_2)
      {
        switch ((nr_of_lines - 2))
        {
        case 1:
        {
          std::cout << "Case 1" << std::endl;
          text_on_one_line(canvas, lines[1], brightness, color[0]);
          break;
        }
        case 2:
        {
          std::cout << "Case 2" << std::endl;
          // new code: v1.5.1
          // if(arrows) {
          //   display_arrows(canvas, lines[1], lines[2], brightness, color[0], color[1]);
          //   arrows = false;
          //   break;
          // }
          // end code: v1.5.1
          text_on_two_lines(canvas, lines[1], lines[2], brightness, color[0], color[1]);
          break;
        }
        case 3:
        {
          std::cout << "Case 3" << std::endl;
          text_on_three_lines(canvas, lines[1], lines[2], lines[3], brightness, color[0], color[1], color[2]);
          break;
        }
        default:
          break;
        }
      }

      std::cout << std::endl;

      // Check for new network settings
      bool success_dhcpcd = false;
      bool success_wpa = false;
      bool should_change_dhcpcd = false;
      bool should_change_wpa = false;

      dhcpcd_lock_file = open(dhcpcd_lock_path, O_WRONLY | O_CREAT);
      if (flock(dhcpcd_lock_file, LOCK_SH) == 0)
      {
        dhcpcd_txt_file.open(dhcpcd_txt_file_path, std::fstream::in);
        if (dhcpcd_txt_file.is_open())
        {
          std::cout << "Successfully opened dhcpcd_txt file to read" << std::endl;
          std::string new_dhcpcd_txt_content[20];
          int k_d = 0;
          k_d = readFromFile(dhcpcd_txt_file, new_dhcpcd_txt_content, "new_dhcpcd_txt");
          dhcpcd_txt_file.close();

          std::cout << "dhcpcd_txt line count= " << k_d << std::endl;
          std::cout << "dhcpcd_conf line count= " << current_dhcpcd_lines << std::endl;

          for (int i = 0; i < k_d; i++)
          {
            if (current_dhcpcd_conf_content[i] != new_dhcpcd_txt_content[i] ||
                k_d != current_dhcpcd_lines)
            {
              should_change_dhcpcd = true;
              current_dhcpcd_conf_content[i] = new_dhcpcd_txt_content[i];
            }
          }

          if (should_change_dhcpcd)
          {
            std::cout << "There are new DHCPCD settings. Changing now." << std::endl;
            dhcpcd_conf_file.open(dhcpcd_conf_path, std::fstream::out | std::fstream::trunc);
            if (dhcpcd_conf_file.is_open())
            {
              success_dhcpcd = true;
              std::cout << "Successfully opened dhcpcd_conf file to write." << std::endl;
              for (int i = 0; i < k_d; i++)
              {
                dhcpcd_conf_file << current_dhcpcd_conf_content[i] << std::endl;
              }
              dhcpcd_conf_file.close();
            }
            else
            {
              current_dhcpcd_conf_content[0] = "";
              std::cout << "Could not open dhcpcd_conf file to write." << std::endl;
            }
          }
        }
        else
        {
          std::cout << "Could not open dhcpcd_txt file to read." << std::endl;
        }
        flock(dhcpcd_lock_file, LOCK_UN);
      }
      else
      {
        std::cout << "Could not lock dhcpcd lock file." << std::endl;
      }
      close(dhcpcd_lock_file);

      wpa_lock_file = open(wpa_lock_path, O_WRONLY | O_CREAT);
      if (flock(wpa_lock_file, LOCK_SH) == 0)
      {
        wpa_txt_file.open(wpa_txt_file_path, std::fstream::in);
        if (wpa_txt_file.is_open())
        {
          std::cout << "Successfully opened wpa_txt file to read." << std::endl;
          std::string new_wpa_txt_content[20];
          int k_w = 0;
          k_w = readFromFile(wpa_txt_file, new_wpa_txt_content, "new_wpa_txt");
          wpa_txt_file.close();

          std::cout << "wpa_txt line count= " << k_w << std::endl;
          std::cout << "wpa_conf line count= " << current_wpa_lines << std::endl;

          for (int i = 0; i < k_w; i++)
          {
            if (current_wpa_conf_content[i] != new_wpa_txt_content[i] ||
                k_w != current_wpa_lines)
            {
              should_change_wpa = true;
              current_wpa_conf_content[i] = new_wpa_txt_content[i];
            }
          }

          if (should_change_wpa)
          {
            std::cout << "There are new WPA settings. Changing now." << std::endl;
            wpa_conf_file.open(wpa_conf_path, std::fstream::out | std::fstream::trunc);
            if (wpa_conf_file.is_open())
            {
              success_wpa = true;
              std::cout << "Successfully opened wpa_conf file to write." << std::endl;
              for (int i = 0; i < k_w; i++)
              {
                wpa_conf_file << current_wpa_conf_content[i] << std::endl;
              }
              wpa_conf_file.close();
            }
            else
            {
              current_wpa_conf_content[0] = "";
              std::cout << "Could not open wpa_conf file to write." << std::endl;
            }
          }
        }
        else
        {
          std::cout << "Could not open wpa_txt file to read." << std::endl;
        }
        flock(wpa_lock_file, LOCK_UN);
      }
      else
      {
        std::cout << "Could not lock wpa lock file." << std::endl;
      }
      close(wpa_lock_file);

      if (success_wpa || success_dhcpcd)
      {
        log("SUCCESS ! Network Settings applied !");
        system("ip link set eth0 down && sleep 1");
        system("ip link set eth0 up && sleep 1");
        system("ip link set wlan0 down && sleep 1");
        system("ip link set wlan0 up && sleep 1");
        system("systemctl daemon-reload && sleep 1");
        system("systemctl restart dhcpcd.service && sleep 1");
        system("systemctl restart DisplayM.service");
      }
      else if (should_change_wpa || should_change_dhcpcd)
      {
        log("ERROR ! Could not apply Network Settings !");
      }

      logs_file.open(logs_file_path, std::fstream::out | std::fstream::trunc);
      // std::cout<<"After logs_file open"<<std::endl;
      if (logs_file.is_open())
      {
        std::cout << "Successfully opened Log file" << std::endl;
        circle.print();
        logs_file << strlog;
        logs_file.close();
        // std::cout<<"After logs_file close"<<std::endl;
      }
      usleep(refresh_interval * 1000 * 999);
      // std::cout<<"After usleep. End of while."<<std::endl;
      std::cout << "--------------------------------------------------------------------" << std::endl;
    }

    canvas->Clear();
    delete canvas;
  }

  return 0;
}
