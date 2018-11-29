<?php

session_start();

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$daticrud = $_SESSION['daticrud'];
$titolo = "MOdifica record in tabella " . $daticrud['aliastabella'];
$script = "";
stampa_head($titolo, "", $script, "MAPSD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='CRUD.php'>ELENCO</a> - $titolo", "", "$nome_scuola", "$comune_scuola");

$id = stringa_html('id');

$daticrud = $_SESSION['daticrud'];
ordina_array_su_campo_sottoarray($daticrud['campi'], 7);
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore connessione!");

//Esecuzione query
$query="select * from ".$daticrud['tabella']." where ".$daticrud['campochiave']." = '".$id."'";
$risgen=mysqli_query($con,$query) or die("Errore:" . $query);
$recgen=mysqli_fetch_array($risgen);

print "<form name='form1' action='CRUDmodregistra.php' method='POST'>";
print "<CENTER><table border ='0'>";

$posarr = 0;
foreach ($daticrud['campi'] as $c)
{
    $posarr++;

    if ($c[7] != 0)
    {

        print "<tr><td>" . $c[6];
        if ($c[9]!="")
            print "<br><small><small>".$c[9]."<big><big>";
        print "</td>";
        if ($c[10] == 1)
            $richiesto = " required";
        else
            $richiesto = "";
        if ($c[2] == '')
            print "<td><input type='" . $c[8] . "' value='".$recgen[$c[0]]."' name='campo[]" . $posarr . "' size='".$c[5]."' ". "' maxlength='".$c[5]."' min='".$c[11]."' ". "' max='".$c[12]."'$richiesto></td></tr>";
        else
        {
            $valore=$recgen[$c[0]];
            print "<td><select name='campo[]" . $posarr . "'$richiesto><option value=''>&nbsp</option>";

            $query = "select " . $c[3] . "," . $c[4] . " from " . $c[2] . " order by " . $c[4];
            print $query;
            $ris = mysqli_query($con, $query);
            while ($rec = mysqli_fetch_array($ris))
            {
                $selected="";
                if ($valore==$rec[$c[3]])
                    $selected=" selected";
                $elcampitabesterna = explode(",", $c[4]);
                $strvalori = "";
                foreach ($elcampitabesterna as $ctb)
                    $strvalori .= $rec[$ctb] . " ";
                print "<option value='" . $rec[$c[3]] . "'$selected>$strvalori</option>";
            }

            print "</select></td></tr>";
        }
    }
}

print "</table>";

print "<center><br><input type='hidden' name='id' value='$id'><input type='submit' name='registra' value='Registra'> </CENTER>";

print "</form>";



stampa_piede("");
mysqli_close($con);

