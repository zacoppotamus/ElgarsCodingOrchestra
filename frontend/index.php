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

        <div id="top" class="header">
            <div class="vertical">
                <h1>Project<strong>Rainhawk</strong></h1>
                <h3>An elegant <em>Visualisation Framework</em> with <em>Cloud Collaboration</em>.</h3>
            </div>
        </div>

        <div id="about" class="intro">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <h2>Big data is all around us.</h2>
                        <p class="lead">Unfortunately, meaning and correlations can be lost when data is stored in different formats in different places.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="services" class="services">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>What's the solution?</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-md-offset-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-cloud"></i>
                            <h4>1. Retrieve</h4>
                            <p>You provide any number of files containing raw data to be uploaded.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-code"></i>
                            <h4>2. Parse</h4>
                            <p>We parse the data using our custom parser and store it in the cloud.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-hdd-o"></i>
                            <h4>3. Manage</h4>
                            <p>You use our management interface (or directly use the API) to manipulate your data.</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="service-item">
                            <i class="service-icon fa fa-tasks"></i>
                            <h4>4. Visualize</h4>
                            <p>Correlations from your data can be graphically understood through our interactive visualizations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="get-started" class="get-started">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h2>What are you waiting for? Let's get started.</h2>
                        <p class="lead">You have two options to get started &mdash; you can either use our web-based data management interface to upload data and create visualizations instantly or, if you're a developer, read our API documentation so that you can build awesome applications using our highly scalable cloud-based API.</p>
                        <p class="buttons">
                            <a href="/login.php?dest=/create.php" class="btn btn-lg btn-primary" role="button">Upload &amp; Visualize Online</a>
                            <em>or</em>
                            <a href="https://www.mashape.com/sneeza/project-rainhawk" class="btn btn-lg btn-primary btn-outline" role="button">API Documentation</a>
                        </p>
                        <p class="dev-note">
                            Note: If you're a developer then you may find <a href="https://github.com/zacoppotamus/ElgarsCodingOrchestra" target="_blank">the source code</a> for the entire project useful &mdash; we've created some wrapper libraries to make communicating with the API as simple as possible to get you started.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div id="team" class="team">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 text-center">
                        <h2>Meet the dream team!</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a href="http://izac.us/"><img class="img-responsive" src="img/team-zac.jpg"></a>
                            <h4>Zac Ioannidis</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a href="http://benelgar.com/"><img class="img-responsive" src="img/team-ben.jpg"></a>
                            <h4>Ben Elgar</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a><img class="img-responsive" src="img/team-oscar.jpg"></a>
                            <h4>Oscar Scull</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a><img class="img-responsive" src="img/team-sam.jpg"></a>
                            <h4>Sam Toussaint</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a href="http://sneeza.me/"><img class="img-responsive" src="img/team-luke.jpeg"></a>
                            <h4>Luke Zbihlyj</h4>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-item fade">
                            <a><img class="img-responsive" src="img/team-steve.jpg"></a>
                            <h4>Stephen Livermore-Tozer</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <p>Made in <strong>Bristol</strong> with ♥ <em>&mdash;</em> <strong>Elgar's Coding Orchestra</strong> <em>&mdash;</em> <?php echo date("Y"); ?></p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="/js/jquery-1.10.2.js"></script>
        <script src="/js/bootstrap.js"></script>
    </body>
</html>
