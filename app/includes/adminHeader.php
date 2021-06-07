<header>
    <a class="logo" href="<?php echo BASE_URL . '/index.php'?>">
        <h1 class="logo-text"><span>Awa</span>Inspires</h1>
    </a>
    <i class="fa fa-bars menu-toggle"></i>
    <ul class="nav">
        <?php if(isset($_SESSION['nome_user'])):?>
            <li>
                <a href="#">
                    <i class="fa fa-user"></i>
                <?php echo $_SESSION['nome_user']; ?>
                    <i class="fa fa-chevron-down" style="font-size: .8em;"></i>
                </a>
                <ul>
                    <li><a href="<?php echo BASE_URL .'/logout.php'?>" class="logout">Logout</a></li>
                </ul>
            </li>
        <?php endif; ?>
            
    </ul>
</header>