<?php

require_once "includes/core.php";

?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <title>Project Rainhawk - Elgar's Coding Orchestra</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once "includes/meta.php"; ?>
        <link rel="stylesheet" href="/css/style.css" type="text/css">
    </head>

    <body>
        <?php require_once "includes/nav.php"; ?>

        <!-- Full Page Image Header Area -->
        <div id="top" class="header">
            <div class="vert-text">
                <h1>Project<strong>Rainhawk</strong></h1>
                <h3>Helping <em>you</em> visualize <em>your</em> data.</h3>
            </div>
        </div>
        <!-- /Full Page Image Header Area -->

        <!-- Intro -->
        <div id="about" class="intro">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <h2>Big Data is the Future.</h2>
                        <p class="lead">Unfortunately meaning and correlations can be lost under the vast amounts of spreadsheets and files.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Intro -->

        <!-- Services -->
        <div id="services" class="services">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <h2>What We Do</h2>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-md-offset-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-cloud"></i>
                            <h4>Retrieve</h4>
                            <p>We mine open data from large and small publicly accessible repositories.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-code"></i>
                            <h4>Parse</h4>
                            <p>We parse data from .xls and .csv files and store it in our database.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-hdd-o"></i>
                            <h4>API</h4>
                            <p>Our database API serves the data in JSON format.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-tasks"></i>
                            <h4>Visualize</h4>
                            <p>Correlations from data served from the database can be understood through our interactive visualizations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Services -->

        <!-- Team -->
        <div id="portfolio" class="portfolio">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <h2>Our Team</h2>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="http://izac.us/"><img class="img-portfolio img-responsive" src="img/team-zac.jpg"></a>
                            <h4>Zac Ioannidis</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="http://benelgar.com/"><img class="img-portfolio img-responsive" src="img/team-ben.jpg"></a>
                            <h4>Ben Elgar</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="#"><img class="img-portfolio img-responsive" src="img/team-oscar.jpg"></a>
                            <h4>Oscar Scull</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="#"><img class="img-portfolio img-responsive" src="img/team-sam.jpg"></a>
                            <h4>Sam Toussaint</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="#"><img class="img-portfolio img-responsive" src="img/team-luke.jpeg"></a>
                            <h4>Luke Zbihlyj</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="portfolio-item">
                            <a href="#"><img class="img-portfolio img-responsive" src="img/team-steve.jpg"></a>
                            <h4>Stephen Livermore-Tozer</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Team -->

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <p>ECO | Made in Bristol with ♥ | <?php echo date("Y"); ?></p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /Footer -->

        <!-- JavaScript -->
        <script src="/js/jquery-1.10.2.js"></script>
        <script src="/js/bootstrap.js"></script>
    </body>
</html>
