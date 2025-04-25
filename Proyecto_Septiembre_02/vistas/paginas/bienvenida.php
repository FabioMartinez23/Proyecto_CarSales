
<?php
ini_set('display_errors', 0);
session_start();
?>
    
    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis molestiae sint excepturi nesciunt architecto consectetur pariatur obcaecati alias harum dolor aliquid repudiandae vitae velit necessitatibus quasi magni, aliquam, in ex.
    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos eius esse totam aperiam molestiae mollitia autem provident sit laborum harum maxime porro tenetur, iusto perspiciatis repellendus nulla recusandae, repellat laudantium?
    Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo ratione dolor expedita obcaecati voluptatibus? Magnam minus veritatis pariatur veniam. Minus animi quisquam magnam esse qui vel asperiores reprehenderit reiciendis quod!
    <h1>
        <?php 
            if(isset($_SESSION['username'])){
                echo 'Bienvenido a la plataforma: <u>'.$_SESSION['username'].'</u>';
            }else{
                echo 'Error';
            }
        ?>
    </h1>

    