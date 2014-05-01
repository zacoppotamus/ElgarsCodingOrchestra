<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand">Project<strong>Rainhawk</strong><?php echo $user ? " / " . htmlspecialchars($user, ENT_QUOTES) : null; ?></a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <?php if($user) { ?>
                <li>
                    <div>
                        <a class="btn btn-default navbar-btn" href="/create.php"><i class="fa fa-file"></i>&nbsp; Create Dataset</a>
                    </div>
                </li>
                <li>
                    <div>
                        <a class="btn btn-default navbar-btn" href="/datasets.php"><i class="fa fa-bars"></i>&nbsp; Browse Datasets</a>
                    </div>
                </li>
                <li>
                    <div>
                        <a class="btn btn-default navbar-btn" href="/login.php?logout"><i class="fa fa-user"></i>&nbsp; Log Out</a>
                    </div>
                </li>
            <?php } else { ?>
                <li>
                    <div>
                        <a class="btn btn-default navbar-btn" href="/login.php"><i class="fa fa-user"></i>&nbsp; Login</a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>