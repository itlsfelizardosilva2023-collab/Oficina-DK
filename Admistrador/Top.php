
<?php
  include_once("./models/contas_models.php");
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K </title>
    <link rel="icon" type="image/jpeg"  href="../views/img/WhatsApp Image 2026-02-27 at 12.49.27.jpeg">
    <link rel="stylesheet" href="../views/css/contas.css?v=1.2">
  </head>
  <body>
    
    <div class="container">
          <aside>
            <div class="top">

            
          
            <div class="close"  id="close-btn">
                <span class="material-icons-sharp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                     <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                    </svg>
                  
                </span>
            </div>
            </div>
        

           

    

          </aside>
        <!-----------------fim do sidebar---------------------->
              <main>

              </main>
               <!------------------end--------------------->

               <div class="right">
                <div class="top">
                    <button id="menu-btn">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                           </svg>
                        </span>
                    </button>

                
                    <div class="theme-toggler">
                      <span class="material-icons active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun-fill" viewBox="0 0 16 16">
                          <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
                        </svg>
                     </span>
                        <span class="material-icons ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-fill" viewBox="0 0 16 16">
                               <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
                            </svg>
                        </span> 
                    </div>

                    
                <div class="profile">
                    <a href="../logout.php">
                       <h3>Logout</h3>
                    </a>
               </div>
               <div class="info">
                            <p>Olá! <b><?php echo htmlspecialchars($_SESSION['usuario_nome']);?></b></p>
                            <small class="text-muted">admin</small>            
                </div>
            </div>
             
             
                 </div>   
                 </div>  
        </div>

    </div>

    <script src="../views/js/contas.js"></script>

 <script id="fade-js">
     document.addEventListener("DOMContentLoaded", function() {
   
    document.body.style.opacity = 0;
    setTimeout(() => {
        document.body.style.opacity = 1;
    }, 50);

    
    const links = document.querySelectorAll("a[href]");
    
    links.forEach(link => {
        link.addEventListener("click", function(e) {
            const href = link.getAttribute("href");

            
            if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) {
                return;
            }

            e.preventDefault();
            document.body.classList.add("fade-out"); 

            
            setTimeout(() => {
                window.location.href = href;
            }, 100); 
        });
    });
   });




  <script src="../views/js/contas.js"></script>
</script>
    </body>
</html>