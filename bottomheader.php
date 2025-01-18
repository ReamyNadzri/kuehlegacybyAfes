
</div>
                    <div class="w3-cell w3-right"> <!--KONGSI-->
                        <a href="addKueh.php" class="w3-bar-item w3-button w3-round-large " style="background: #228B22; margin-left :6px; margin-right: 10px ;width: 94%; text-align: left; color: white;">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAADvElEQVR4nO2bSWgUQRSG2yVxOYiCKHhwQVExIm45KUZPguLFs6JeRczJgwt4c01UhLiBBsQtghqNUS8SvKiI6EURFBHcBUHccEvySWHN8KbSy3S6ZqZ67O+S0PW6qv6/p6pra8+rYoABwHRgMVAHDPT+B/gnfD3wkkLeA5uAwV61AtQAZwnnBlAblVED0Aa8AbqpDL+B3THFXzTy+Azc138lzWGZHMUdviYQfwwYrtOHA60i7Rcw1i8jl8SrSu4sQnwtcMm4d2+ASY9EzGq/n72kHZgX2V4qSLHiRfx+EbfVTGyT4j3H0eLbixWv7zklYjeaiarDyzHPc1/85Zji1Zjgh4ifbwbI3r62ysRPBF6I+DtqvGAG5fHcFn/FEN8UU/wXYIZfoNMGWBKvWBEUnMdzDGAI0EFy8YrWVBmAHfF/xP+qr5uUCgO0+KsJxX/TM8IH4toG5w2wKL5Bp+0Q1w84bUDAq25fxD3jgeci/juwJGAU2HeS5YoB+sl32nryOn0Y8FSkr3XSAGAocM2yeLVAcsJIH+2cASUUf8TIs3AS5IIBWvz1Mog/H7g2SIUM0B1eZ5nE14RlmseyxrAyBxnT8FKJPxe5GIrAosaoMpuNih7sh/hFEeJPK6OLqUweS/qiyltHIWfC1u77Kf5kUeIV8i6vxADT9EAlx031/k8wyFHiW3wMLX4PgDIZoCoF3BXFPQNG9PfJ65gthvjjsXeCKJ8B20RRP8OW34DJUeJ13EVjOTz+NhhlMACYozc8cjSGxM7X21mh4kWTugBs7rPU5ZgBXaKYrqDKAkvVpoixjOUr3mbl8pQofyVKLlDM8olRnVmj8St5C8wtRZ0KKKUBWpjs+Pb7xIzxGQ4/BibYrk8lDFgpsv8EjDTMWQN8MMTfAkbZrkulDLgnst8lrqutt9uGcNU8ttvcxwf26E50d9kNAGYar71xwGw9UOkxxD/ps2Njpw5qo1XxqxIGNImsH+p23msIV53ePrVyY7PsWNoogQF61PeOYHr0NHWKrTJdM2B5gPBeva1dZ6ssVw1YZQj/qKfAU22V4boBQ3UfoMbny0JXZKrRAFfIDBAkcymlZAYIkrmUUjIDBMlcSimZAYJkLqWUzABBMpdSSmaAIJlLKSUzQBDmUncaDkv38wBGju6wwDdpOS4fB6Be6HoVFnhOBHZ4VYLxRcnpsMBFsq3ok9n1aWwO+mdf73PGeGHUjYepXlqKca4GOET10RL3tMhC/QXm6wp+OJkEVedXevdpQdHCMzK8/4q/u2PEUlJFCNwAAAAASUVORK5CYII=" alt="create-new--v2" style="padding-bottom:3px;margin-right:8px;text-align:center; width:22px">
                            Kongsi
                        </a><br>

                    </div>

                    <?PHP
                    if (empty($_SESSION['username'])) {
                    ?>

                        <div class="w3-cell w3-right" style="margin-right: 15px;"> <!--LOGIN (HIDDEN)-->
                            <a href="userLogin.php" class="w3-bar-item w3-button w3-round-large w3-border" style="margin-left :6px ;width: 94%; text-align: left;">
                                LogMasuk
                            </a><br>

                        </div>

                    <?PHP
                    } else {
                    ?>

                        <div class="w3-cell w3-right" style="margin-right: 15px;"> <!--logout (HIDDEN)-->
                            <a href="logout.php" class="w3-bar-item w3-button w3-round-large w3-border" style="margin-left :6px ;width: 94%; text-align: left;">
                                LogKeluar
                            </a><br>

                        </div>
                        <a href='userProfile.php'>
                            <div class="w3-cell w3-right text-black" style="margin-right: 15px; "> <!--PROFILE (HIDDEN)-->
                                <?php
                                if (isset($_SESSION['google_user'])) {
                                    $profilegambor = $_SESSION['google_user']['picture'];
                                    echo "<img src='$profilegambor'class='w3-circle' alt='test-account' style='text-align:center; width:40px'>";
                                } else {
                                    echo "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFdUlEQVR4nO1aS4hcRRR9UaPJxs9CVBT8oBmDEsVPFE0iGJxEMbixySKSTt+qe9+7t6dHRlQQlMadGozfGH8ouHAnoi5cmLjwFxJJ1CgiMaMjRpB8FxLJZybK7e6Zed0zk3RVve6ZhHegYJjX737Ou1V1696Kohw5cuTIkaOjIOq7FogTg7IBSDYZlEFAOQAkR2uj9jfv0mdA/JpBiSFJeqJTGdbKjUC8zqDsNiT/+Qwg/tOgPF+K+26IThHMMsQrgGSzr9NTDuRvrC3frzqimQgTxwsN8reZO94aFShbgMq3RDMFhYGBuUD8CiCPdNr5cRJ4BIhfKhaLc6bVeUiSHiD5oVuOT7JGfLcmjq+ZFudLJLcZ5H3T5XxqShwokSzqrvOY3GuQ/51u58cjQQ4ZkuXdcZ5kUbjz/Bcgvw8kTwMKAMlSnU7W9l3JzBfoUF0G5RcXEhD5jo46D0nSY4gPejmtOwRKnyZF7eozyM85RsL+jq0Jhdpq77Pg8Q79wt7R5ky0bO/I7gAor3oY83aIMYVC4UxDvMcj4l7MPMkB532e38wiazPE73oQMLyGyjdll96iW4anU6VSqZyThfJSqXIhED8CyIcdbdicSdpsiFe4fgFry8uijAHEFVc7dLvOQLG4HWxQdnbiwEJEs3X7dIyCr4OPtMaRdSB5NuoQgPgt5/XA8IIQheucwz+WB9IySjGvNsRDhvg9Q9IPxL3Wlufp3NZ1QncJY/ovMobn21geNCjPAMnPBmWg1R5DXPJYENd6E2B8ihmG5zeFrWdBRHcda/nmJnsML/CIyD8Csj5xNpyIzhszOO6700dGioRP0zYR0SU+cryyQ9Aanh8Bs8cIQH4ikICR1XF86ag8nTI+ciwxORNgUDb4KGuSQfJhCAH1UbYtMn3krHcngOTzcAJ4WygBgPJOMAHIG30i4PdQAoD472ACSL4IJ0AGPQhgr2pPi4zwognK7lACgGSvOwEkR0JDzZAcDyaA5EgLqRudCUA+7EwAOB4+Ws8AYGVVBs43Bj+UInV5dwgg2RuSA2gCkiEBQ6Nyi8WHz/d4f48zAQZl0FXRqkrl3BAC25nDSrL7+/yrTwRscjY2jheOEUjyVIYEPDkq19rkdg8Zn3WlBAbEr2eRTTbJtIItdr3hLAflZWcCDErsYfBwugBarVbPAOLfvAlAGVQZ44TKUtURSmJbsLY8zzNcD2nXaPyL8aMBBIwdiVVmowHiLsfI1c4EhGSDhuSj8VW7OEcvP3g4vzNdVwTiT3yjKPKFQX7Bk4Dj6QWxhHwXIB9ziKKj6X5fY+HzSqq0qONNACTJ9d7hS7yt6QsiS7vkpY+vtSMwynZfO6yV67wJUGhh0Z+E5mOozukT9Rcaz/qb3iFZ76s/uCg61gn2J0CNWBKlYGK5T1vak/xuf2uH19pkcYjuTMriCkD+yj8EJ/YItAucXtQA5eNiHF8x8XflZQFT8Mso0/s/5L7/6kDkW3316rteUYd8LPN7RIZkrcdXGCqKXDwqQ3sGWticSoc+S/cV9N16SX0G9CYKAwNzDfL37RnAu0oxm0KhcHZaRv2AxAeB5LF0sVP/NsiPN541FS9UhspqN5fQe0Nqa+YEKADk8hO2q1EGdQurVqtnRS3QlLbNaTScTn+b3+dCrWkypX7eV2K+KuokgGTJxJSUdxjilZMZ7nOO199OJadBxMq6zgkpeHcuS1mbLAbkf7QNrq0vvchwsnf0y7RLgO4SDrdSt6rz1iZ3R92EtZXLXLrA2uZqn4DmlthJMKthy8wGEPe2vZLH5Xui0w1A3NvOgai+h3NvdDqiqPt67TwgHwDxT7XOcX38qP/TZ+m8IUeOHDly5MiRI0fUOfwPWUs8T7LFLncAAAAASUVORK5CYII=' alt='test-account' style='text-align:center; width:40px'>";
                                }
                                echo "  Hi, " . $_SESSION['username'];
                                ?>

                            </div>
                        </a>
                    <?PHP
                    }
                    ?>
                </div>


                <div class="w3-round-large" style="height: 92%; margin-top: 4%">

                    <!--CONTENT START HERE-->
                    <div class="w3-container w3-white" style="margin-top:5px;width: 100%; height: 100%;">

                        <!--<div href="debug.html" class="w3-panel w3-yellow w3-border w3-animate-fading">
                                    <h3>Development Phase Notification</h3>
                                    <p>This website is currently under development. Some features may not be fully functional or available at this time. We appreciate your understanding and welcome your feedback to improve the platform.</p>
                                    <?php
                                    //include ('debug.html'); 
                                    //include ('popup.php'); 
                                    ?>



                                </div>-->