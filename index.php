<?php
session_start();

function Securise($string){
    //on regarde si le type de string est un nombre entier (int)
    if(ctype_digit($string)){
        $string = intval($string);
    }else{
        //pour les autres types
        $string = strip_tags($string);
        $string = addcslashes($string, '%_');
    }
    return $string;
}

function PasswordHash($str){
    $str = sha1(md5('njhuhufhfjsqofj'.$str));
    return $str;
}

//bdd
try{
    $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}catch (Exception $e){
    echo "Erreur : ".$e;
}

//formulaire d'inscription
if(isset($_POST['Envoie'])){
    $nom = Securise($_POST['nom']);
    $prenom = Securise($_POST['prenom']);
    $email = Securise($_POST['email']);
    $mdp = Securise($_POST['mdp']);
    $mdp2 = Securise($_POST['mdp2']);
    $age = Securise($_POST['age']);
    $genre = Securise($_POST['genre']);
    $date = date('d/m/Y à H:i:s');
    if(!empty($nom) AND !empty($prenom) AND !empty($mdp) AND !empty($mdp2) AND !empty($email) AND !empty($age) AND !empty($genre)){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            if($mdp == $mdp2){
                if($genre == "H" OR $genre == "F"){
                    if(strlen($nom) <= 50){
                        $testemail = $bdd->query('SELECT id FROM users Where email ="'.$email.'"');
                        if($testemail-> rowCount() < 1){
                                if(is_int($age)){
                                $mdp = PasswordHash($mdp);
                                $bdd->query('INSERT INTO users (nom, prenom, email, mdp, genre, age, date) VALUES ("'.$nom.'", "'.$prenom.'","'.$email.'", "'.$mdp.'", "'.$genre.'","'.$age.'", "'.$date.'")');
                                $return = "Utilisateur crée";
                                }else $return = "L'âge est invalide";
                        }else $return = "L'email est déjà utilisé";
                    }else $return = "Le nom est trop long";
                }else $return = "Le genre est invalide";
            }else $return = "Les deux mots de passe ne correspondent pas";
        }else $return = "Email est invalide";
    }else $return = "Un ou plusieurs champs est manquant.";
}
//Connexion
if(isset($_POST['login'])){
    $email = Securise($_POST['email']);
    $mdp = Securise($_POST['mdp']);
    if(!empty($email) AND !empty($mdp)){
        $mdp = PasswordHash($mdp);
        $VerifUser = $bdd->query('SELECT id FROM users WHERE email = "'.$email.'" AND mdp = "'.$mdp.'"');
        $UserData = $VerifUser->fetch();
        if($VerifUser->rowCount() == 1){
            $_SESSION['login'] = $UserData['id'];
            $return = "Vous êtes bien connecté";
            header('location:admin.php');
        }else $return = "Les identifiants sont invalides.";
    }else $return = "Un ou plusieurs champs sont manquants.";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Formulaire en HTML/PHP avec serveur Wamp</title>
</head>

<body>
<?php if(isset($_POST['Envoie']) AND isset($return)) echo $return; ?>
    <form action="#" method="POST">
    <select name="genre">
            <option value="H">Masculin</option>
            <option value="F">Féminin</option>
    </select>
    <p> Ca marche !</p>
        <p>Votre nom : <input type="text" name="nom" placeholder="Votre nom" /></p>
        <p>Votre prénom : <input type="text" name="prenom" placeholder="Votre prénom" /></p>
        <p>Votre âge : <input type="text" name="age" placeholder="Votre âge" /></p>
        <p>Votre email : <input type="email" name="email" placeholder="Votre e-mail" /></p>
        <p>Votre mot de passe : <input type="password" name="mdp" placeholder="Votre mot de passe" /></p>
        <p>Veuillez confirmez votre mot de passe : <input type="password" name="mdp2"
                placeholder="Confirmez votre mot de passse" /></p>
        <p><input type="submit" name="Envoie" value="Envoyer" /></p>
    </form>
    <hr>
    <?php if(isset($_POST['login']) AND isset($return)) echo $return; ?>
    <form action="#" method="POST">
        <p>Votre email : <input type="email" name="email" placeholder="Votre e-mail" /></p>
        <p>Votre mot de passe : <input type="password" name="mdp" placeholder="Votre mot de passe" /></p>
        <p><input type="submit" name="login" value="Connexion" /></p>
    </form>
</body>

</html>