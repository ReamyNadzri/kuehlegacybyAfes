<?php 
include ('header.php'); 
//include ('popup.php'); 
?>



<body class="" style="background-color: #FFFAF0;">
    
        <link rel="stylesheet" href="style.css">
        <title>Legacy Kueh System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css">
        <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">

        <div class="w3-row">
            <div class="w3-col s1 w3-text-white w3-left" style="width: 10%; height: 10%">/</div>
            <div class="w3-col s1 w3-text-white w3-right" style="width: 10%; height: 10%">/</div>
            <div class="w3-rest w3-white" style="">


                <div class="w3-container w3-center" style="margin-top: 2%;">
                    <img src="sources/header/logofull.svg" alt="" width="500px">
                    
                    <form action="kuehListing.php" method="get">
                        <div class="w3-center" style="text-align: center">
                            <button class="w3-button w3-hover-green w3-border w3-round-large" name="search" value="a" id="">Mulakan Carian Resipi</button>
                        </div>
                    </form>

                </div>



                <hr>
                <h3 style="margin-left: 15px;"><b>Bahan Popular</b></h3>

                <div class="w3-row w3-text-white w3-large w3-text-shadow w3-center" style="position:static">
                  <a href="kuehListing.php?search=almond">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; position: relative; z-index: 1; animation-delay: 0.0s">
                        <img src="sources/index/almond.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover;">
                        <div class="w3-display-bottomleft w3-container"><p>Kacang Almond</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=daun">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.2s">
                        <img src="sources/index/daunpisang.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover;">
                        <div class="w3-display-bottomleft w3-container"><p>Daun Pisang</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=melaka">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.4s">
                        <img src="sources/index/gulamelaka.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover;">
                        <div class="w3-display-bottomleft w3-container"><p>Gula Melaka</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=kari">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.6s">
                        <img src="sources/index/kari.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover;">
                        <div class="w3-display-bottomleft w3-container"><p>Daun Kari</p></div>
                    </div>
                  </a>
                </div>
                
                <div class="w3-row w3-text-white w3-large w3-text-shadow w3-center">
                  <a href="kuehListing.php?search=kelapa">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.2s">
                        <img src="sources/index/kelapa.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container"><p>Isi Kelapa</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=pandan">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.4s">
                        <img src="sources/index/pandan.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container"><p>Daun Pandan</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=pisang"></a>
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.6s">
                        <img src="sources/index/pisang.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container"><p>Pisang</p></div>
                    </div>
                  </a>
                  <a href="kuehListing.php?search=ubi">
                    <div class="w3-col s3 w3-display-container w3-animate-zoom" style="padding: 5px; animation-delay: 0.8s">
                        <img src="sources/index/ubi.jpg" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 150px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container"><p>Ubi Kayu</p></div>
                    </div>
                  </a>
                </div>
                <hr>
                <br>
                <h3 style="margin-left: 15px;"><b>Resipi lama yang mengimbau 1001 kenangan bersama!</b></h3>

                <h4 style="margin-left: 15px;">Kuih-muih dari Terengganu</h4>         <!--SEARCHING USING DB-->
                <div class="w3-row w3-text-white">
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/chekMekMolek.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Chek Mek Molek</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                      </div>
                    </div>
                  
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/kayuKeramat.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Kayu Keramat</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                      </div>
                    </div>
                  
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/pulutNyior.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Pulut Nyior</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                        
                      </div>
                    </div>
                    <div class="w3-col s3" style="padding: 5px;">
                        <div class="w3-card w3-round-large w3-display-container">
                          <img src="sources/index/tokHajiSerban.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                          <div class="w3-display-bottomleft w3-container">
                            <h4>Tok Haji Serban</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                        </div>
                    </div>
                </div>

                <hr>

                <h4 style="margin-left: 15px;">Kuih-muih dari Johor</h4>         <!--SEARCHING USING DB-->
                <div class="w3-row w3-text-white">
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/chekMekMolek.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Chek Mek Molek</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                      </div>
                    </div>
                  
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/kayuKeramat.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Kayu Keramat</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                      </div>
                    </div>
                  
                    <div class="w3-col s3" style="padding: 5px;">
                      <div class="w3-card w3-round-large w3-display-container">
                        <img src="sources/index/pulutNyior.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                        <div class="w3-display-bottomleft w3-container">
                            <h4>Pulut Nyior</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                        
                      </div>
                    </div>
                    <div class="w3-col s3" style="padding: 5px;">
                        <div class="w3-card w3-round-large w3-display-container">
                          <img src="sources/index/tokHajiSerban.jpg" class="w3-round-large" style="height: 200px; width: 100%; object-fit: cover">
                          <div class="w3-display-bottomleft w3-container">
                            <h4>Tok Haji Serban</h4>
                            <p class="w3-small">Dari Mak Kita</p>
                        </div>
                        </div>
                    </div>
                </div>



            </div>
            
        </div>

        
<?php 
          include ('footer.php'); 
          include ('popup.php'); 
        ?>



                                </div> <!--must include in next page-->
                            </div>
                        </div>
                    </div>      
                </div>
        </body><!--until here-->

        