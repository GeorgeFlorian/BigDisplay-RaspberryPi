<!DOCTYPE html>
<html lang="en">

<?php
include(__DIR__ . "/config/inputs.php");
include(__DIR__ . "/config/log.php");
include(__DIR__ . "/config/version.php");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Chrome, Firefox OS and Opera -->
    <link rel="shortcut icon" href="/favicon.ico?v=476mA4zprB" />
    <!-- Tab Color iOS Safari -->
    <meta name="apple-mobile-web-app-title" content="Metrici.ro" />
    <meta name="application-name" content="Metrici.ro" />
    <!-- Tab Color Android Chrome -->
    <meta name="theme-color" content="#e11422" />
    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/icomoon-style.css">
    <!-- JavaScript -->
    <script defer src="./javascript/jquery-3.6.0.min.js"></script>
    <script defer src="./javascript/main.js"></script>
    <title>Display Interface</title>
</head>

<body>
    <a href="https://www.metrici.ro/" target="_blank" class="mini-logo"></a>
    <nav>
        <div class="nav_container">
            <a href="https://www.metrici.ro/" target="_blank" class="logo"></a>
            <ul>
                <li><a href="/index.php" class="active">Home</a></li>
                <li><a href="/static_display.php">Static Text</a></li>
                <li class="dropdown">
                    <a class="touch">
                        Network Settings
                        <span class="icon-circle-down"></span>
                        <span class="icon-circle-up"></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="/dhcp.php">DHCP IP</a>
                        <a href="/static_ip.php">Static IP</a>
                    </div>
                </li>
                <li><a href="/update.php">Update</a></li>
            </ul>
        </div>
    </nav>

    <section>
        <div class="top_container">
            <div class="left_container">
                <div class="title">
                    <h1>Current Settings</h1>
                </div>
                <div class="inner_container">
                    <?php writeConfigToPage() ?>
                    <div style="height: 30px;"></div>
                </div>
            </div>

            <div class="right_container">
                <div class="title">
                    <h1>Change Settings</h1>
                </div>
                <div class="inner_container">
                    <form method="post">
                        <div class="input_row">
                            <label class="label_" for="getURL">URL Link:</label>
                            <input class="input_text" id="getURL" type="url" inputmode="url" placeholder="Enter URL"
                                name="getURL" value="" title="URL" />
                        </div>
                        <div class="input_row">
                            <label class="label_" for="getURR">URL Refresh Interval (s):</label>
                            <input class="input_text" id="getURR" inputmode="numeric"
                                title="Enter a number of seconds between 1 and 99" type="text" pattern="([0-9]{1,2})"
                                oninvalid="this.setCustomValidity('Please enter a number between 1 and 99')"
                                onchange="try{setCustomValidity('')}catch(e){}" oninput="setCustomValidity(' ')"
                                maxlength="2" placeholder="Enter URL Refresh Interval in seconds" name="getURR"
                                value="">
                        </div>
                        <div class="input_row">
                            <label class="label_" for="getBrightness">Brightness (&#37;):</label>
                            <input class="input_text" id="getBrightness" inputmode="numeric" type="text" maxlength="2"
                                pattern="([0-9]{1,2})"
                                oninvalid="this.setCustomValidity('Please enter a number between 2 and 99')"
                                onchange="try{setCustomValidity('')}catch(e){}" oninput="setCustomValidity(' ')"
                                placeholder="Enter brightness (2-99)" name="getBrightness" value=""
                                title="Enter a Brightness value between 2 and 99">
                        </div>
                        <input class="button" type="submit" name="save_settings" value="Save All Values" />
                    </form>
                </div>
            </div>
        </div>

        <div class="bottom_container">
            <div class="title">
                <h1>Logs</h1>
            </div>
            <div class="log_container">
                <?php echo $log_string; ?>
            </div>
        </div>

    </section>

    <footer>
        <form method="post" action="">
            <input class="button" type="submit" name="restart_device" value="Restart Device" />
        </form>
        <span class="version">
            <a href="https://www.metrici.ro/products/metrici-display" target="_blank"
                title="Go to Display documentation">Display Controller</a> 
                - Version: <?php echo $version; ?>
        </span>
        <span class="copyright">
            <a href="https://www.metrici.ro/" target="_blank"
                title="Go to Metrici Website">Metrici</a> © 2021 -
            All Rights Reserved.
        </span>
    </footer>
</body>

</html>