<!DOCTYPE html>
<html lang="en">

<?php
include(__DIR__ . "/config/inputs.php");
include(__DIR__ . "/config/log.php");
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="index.css">
    <script defer src="general.js"></script>
    <title>Static Display Text</title>
</head>

<body>
    <a href="https://www.metrici.ro/" target="_blank" class="mini-logo"></a>
    <nav>
        <div class="nav_container">
            <a href="https://www.metrici.ro/" target="_blank" class="logo"></a>
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/static_display.php" class="active">Static Text</a></li>
                <li class="dropdown">
                    <a class="touch">Network Settings <i class="fas fa-angle-down"></i><i
                            class="fas fa-angle-up"></i></a>
                    <div class="dropdown-content">
                        <a href="/dhcp.php">DHCP IP</a>
                        <a href="/static_ip.php">Static IP</a>
                    </div>
                </li>
                <!-- <li><a href="/files">Import/Export Files</a></li> -->
                <li><a href="/update.php">Update</a></li>
            </ul>
        </div>
    </nav>

    <section>
        <div class="top_container">
            <div class="left_container">
                <div class="title">
                    <h1>Current Static Text</h1>
                </div>
                <div class="inner_container">
                    <?php displayLinesOnPage() ?>
                    <div style="height: 30px;"></div>
                </div>
            </div>

            <div class="right_container">
                <div class="title">
                    <h1>Static Display Text</h1>
                </div>
                <div class="inner_container">
                    <form method="post">
                        <div class="input_container">
                            <div class="input_row">
                                <label class="label_" for="getLine1">Text on first line</label>
                                <input class="input_text" id="getLine1" type="text" placeholder="Enter text on first line"
                                    name="getLine1" value="" title="Text on first line" />
                            </div>
                            <div>
                                <input class="colors" name="line_1_color" list="line_1_color" placeholder="Pick a color">
                                <datalist id="line_1_color">
                                    <option value="Red">
                                    <option value="Orange">
                                    <option value="Yellow">
                                    <option value="Green">
                                    <option value="Blue">
                                    <option value="Indigo">
                                    <option value="Violet">
                                    <option value="White">
                                </datalist>
                            </div>
                        </div>
                        <div class="input_container">
                            <div class="input_row">
                                <label class="label_" for="getLine2">Text on second line</label>
                                <input class="input_text" id="getLine2" type="text" placeholder="Enter text on second line"
                                    name="getLine2" value="" title="Text on second line" />
                            </div>
                            <div>
                                <input class="colors" name="line_2_color" list="line_2_color" placeholder="Pick a color">
                                <datalist id="line_2_color">
                                    <option value="Red">
                                    <option value="Orange">
                                    <option value="Yellow">
                                    <option value="Green">
                                    <option value="Blue">
                                    <option value="Indigo">
                                    <option value="Violet">
                                    <option value="White">
                                </datalist>
                            </div>
                        </div>
                        <div class="input_container">
                            <div class="input_row">
                                <label class="label_" for="getLine3">Text on third line</label>
                                <input class="input_text" id="getLine3" type="text" placeholder="Enter text on third line"
                                    name="getLine3" value="" title="Text on third line" />
                            </div>
                            <div>
                                <input class="colors" name="line_3_color" list="line_3_color" placeholder="Pick a color">
                                <datalist id="line_3_color">
                                    <option value="Red">
                                    <option value="Orange">
                                    <option value="Yellow">
                                    <option value="Green">
                                    <option value="Blue">
                                    <option value="Indigo">
                                    <option value="Violet">
                                    <option value="White">
                                </datalist>
                            </div>
                        </div>
                        <input class="button" type="submit" name="save_text" value="Save All Values" />
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
        <span class="version"><a href="https://www.metrici.ro/products/metrici-display" target="_blank"
                title="Go to Display documentation">Display Controller</a> - Version: 1.5.3</span>
        <span class="copyright"><a href="https://www.metrici.ro/" target="_blank"
                title="Go to Metrici Website">Metrici</a> © 2021 -
            All Rights Reserved.</span>
    </footer>
</body>

</html>