

<?php
// create a web application that simulates the ranking of four teams during their participation in the group stage of the world cup

// create a "partidos" array for storage the data of the matches by default

// confition  json_decoder to convert a JSON object to a PHP object

// else $pardidos have a Values  
if(isset($_COOKIE['losDatosDePartidos'])) {

    $pardidos = json_decode($_COOKIE['losDatosDePartidos'], true);

}else {

    $pardidos = array( 
    "MOROCCOvsCROATIA" =>
    array("MOROCCO" => 0, "CROATIA" => 0,"STATUS" => false),
    
    "BELGIUMvsCANADA" => 
    array("BELGIUM" => 0, "CANADA" => 0, "STATUS" => false),
    
    "MOROCCOvsBELGIUM" => 
    array("BELGIUM" => 0, "MOROCCO" => 0,"STATUS" => false),
    
    "CROATIAvsCANADA" => 
    array("CROATIA" => 0, "CANADA" => 0,"STATUS" => false),
    
    "CROATIAvsBELGIUM" => 
    array("CROATIA" => 0, "BELGIUM" => 0, "STATUS" => false),

    "MOROCCOvsCANADA" => 
    array("MOROCCO" => 0, "CANADA" => 0,"STATUS" => false)
    );
}
// creat a $flags array just for add a flags on any team 
$flags = array(
    "MOROCCO" => "img/morocco",
    "CROATIA" => "img/Croitia",
    "BELGIUM" => "img/Belgium",
    "CANADA" => "img/Canada"
);
// condition to take values the inputs and set in cookie
// json_encode to encode a value to JSON format 
if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['matchName'])){
foreach($pardidos as $key => $value){
    $pardidos[$key][$_POST[$key][0]] = $_POST[$key][1];
    $pardidos[$key][$_POST[$key][2]] = $_POST[$key][3];
    $pardidos[$key]['STATUS'] = true;
};
setcookie("losDatosDePartidos", json_encode($pardidos), time() + 60 * 60 * 24 * 30);
};
// this condition for recovry the array $pardidos on status by default
if( $_SERVER['REQUEST_METHOD'] == 'POST' 
&& isset($_POST['RESET'])){
    $pardidos = array( 
        "MOROCCOvsCROATIA" =>
        array("MOROCCO" => 0, "CROATIA" => 0,"STATUS" => false),
        
        "BELGIUMvsCANADA" => 
        array("BELGIUM" => 0, "CANADA" => 0, "STATUS" => false),
        
        "MOROCCOvsBELGIUM" => 
        array("BELGIUM" => 0, "MOROCCO" => 0,"STATUS" => false),
        
        "CROATIAvsCANADA" => 
        array("CROATIA" => 0, "CANADA" => 0,"STATUS" => false),
        
        "CROATIAvsBELGIUM" => 
        array("CROATIA" => 0, "BELGIUM" => 0, "STATUS" => false),
        
        "MOROCCOvsCANADA" => 
        array("MOROCCO" => 0, "CANADA" => 0,"STATUS" => false)
    );
setcookie("losDatosDePartidos", json_encode($pardidos));
};
// function to remove the item[1] and "STATUS" from $pardidos for know the goals received of the item[0]
function getTheReceived( $array , $primerEquipo ){
    $arrayData = $array ;
        unset($arrayData[$primerEquipo]);
        unset($arrayData["STATUS"]);
foreach ($arrayData as $key => $value) {
    return $key ;
}
}
/* function to build a another a array from $pardidos and add a variables ($POINTS, $GAME_PLAYED....) and i will loop  forEach team and calculate all the points for every team at the end it will return one array containing all the teams with there data PONITS GOALS DIFF ... so it's an array of all the teams with thiere data Fill in the variables with values to display in the table*/
function resultadosPartidos($pardidos){
    $equipos = [];
    // this codes used to get the available teams from the $pardidos
    $TheTeamsKeys = array();
    foreach($pardidos as $key => $val) {
        $countries = array();
        $values = array();
     foreach($val as $valkey => $miniVAL){
        if($valkey != "STATUS"){
            array_push($countries, $valkey);
            array_push($values, $miniVAL);
            array_push($TheTeamsKeys, $valkey);
        }
     }
    }
    $TheTeamsKeys = array_unique($TheTeamsKeys);
    foreach($TheTeamsKeys as $value){
        $equipos += [$value => array("POINTS" => 0 , "GAMES_PLAYED" => 0 , "GAMES_WON" => 0  , "GAMES_EQUAL" => 0  , "GAME_LOSTS" => 0  , "GOALS_SCORED" => 0  , "GOALS_RECEIVED" => 0  , "DIFF" => 0 )];
    }
    foreach ($equipos as $key => $value) {
        $GAMES_PLAYED = 0 ;
        $GAMES_WON = 0 ;
        $GAMES_EQUAL = 0 ;
        $GAME_LOSTS = 0 ;
        $POINTS = ( $GAMES_WON * 3 ) + ( $GAMES_EQUAL * 1 ) ;
        $GOALS_SCORED = 0 ;
        $GOALS_RECEIVED = 0 ;
        $DIFF = $GOALS_SCORED - $GOALS_RECEIVED ;
        foreach ($pardidos as $DataKey => $DataValue) {
            if(isset($DataValue[$key])){
                $GOALS_SCORED += $DataValue[$key] ;
                $GOALS_RECEIVED += $DataValue[getTheReceived( $DataValue , $key )] ;
                if($DataValue["STATUS"] == true){
                    $GAMES_PLAYED += 1 ;
                }
                if( $DataValue[$key] > $DataValue[getTheReceived( $DataValue , $key )] ){
                    $GAMES_WON += 1;
                } elseif( $DataValue[$key] < $DataValue[getTheReceived( $DataValue , $key )] ){
                    $GAME_LOSTS += 1;
                } elseif( $DataValue[$key] == $DataValue[getTheReceived( $DataValue , $key )] ){
                    $GAMES_EQUAL += 1;
                } 
            }
        }
        $equipos[$key]["GOALS_SCORED"] = $GOALS_SCORED ; 
        $equipos[$key]["GOALS_RECEIVED"] = $GOALS_RECEIVED ; 
        $equipos[$key]["DIFF"] = $GOALS_SCORED - $GOALS_RECEIVED ; 
        $equipos[$key]["GAMES_PLAYED"] = $GAMES_PLAYED ; 
        $equipos[$key]["GAMES_WON"] = $GAMES_WON ; 
        $equipos[$key]["GAME_LOSTS"] = $GAME_LOSTS ; 
        $equipos[$key]["GAMES_EQUAL"] = $GAMES_EQUAL ; 
        $equipos[$key]["POINTS"] =  ( $GAMES_WON * 3 ) + ( $GAMES_EQUAL * 1 );
    }
    return cambiadorDeDatos($equipos);
};
/*  function to change data for add a team in $arrayForm */
function cambiadorDeDatos($data) {
    foreach($data as $key => $value) {
        foreach ($value as $xkey => $xvalue) {
            $data[$key]["Team"] = $key ;
        }
    }
    $arrayForm = [];
    foreach($data as $key => $value) {
      array_push($arrayForm , $value );
    }
    // echo "<pre>";
    // print_r( $arrayForm);
    // echo "<pre>";
    return $arrayForm;
}
/* this function just for sort the POINTS && DIFF && GOALS_SCORED  */ 
function clasificar($data){
    usort($data, function ($x, $y) {
        global $pardidos;
        if ($x["POINTS"] === $y["POINTS"]) {
            if ($x["DIFF"] === $y["DIFF"]) {
                if ($x["GOALS_SCORED"] === $y["GOALS_SCORED"]) {
                    foreach ( $pardidos as $matcheKey => $matcheValue) {
                        if(isset($matcheValue[$x["Team"]])  && isset( $matcheValue[$y["Team"]] )){
                            if ( $matcheValue[$x["Team"]] === $matcheValue[$y["Team"]]) {
                        return 0;
                    } else if ( $matcheValue[$x["Team"]] < $matcheValue[$y["Team"]] ) {
                        return 1 ;
                    } else if ( $matcheValue[$x["Team"]] > $matcheValue[$y["Team"]] ) {
                        return -1 ;
                    }
                        }
                    }
                } else if ( $x["GOALS_SCORED"] < $y["GOALS_SCORED"] ) {
                    return 1 ;
                } else if ( $x["GOALS_SCORED"] > $y["GOALS_SCORED"] ) {
                    return -1 ;
                }
            } else if ( $x["DIFF"] < $y["DIFF"] ) {
                return 1 ;
            } else if ( $x["DIFF"] > $y["DIFF"] ) {
                return -1 ;
            }
        } else if ( $x["POINTS"] < $y["POINTS"] ) {
            return 1 ;
        } else if ( $x["POINTS"] > $y["POINTS"] ) {
            return -1 ;
        }
    });
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>PHP</title>
</head>
<body>
<style>
    *{ margin:0;padding:0;}
    img{width: 50px;}
    #bgInput{width: 200px;}
    button{width:50%;margin:0 auto;}
</style>
<section class="container row justify-content-between mx-auto flex-row">
<section class="col-md-6">
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
<?php
// foreach for loop in array $pardidos and take the keys and values and push in a variables $teams & $values 
// and build the content
foreach($pardidos as $key => $value){
$teams = array();
$values = array();
    foreach($value as $xkey => $xvalue){
// push in the variables just teams and values 
        if($xkey != "STATUS"){
            array_push($teams, $xkey);
            array_push($values, $xvalue);
        }
    }
?>
<!-- add in each tag <img> <span> <input type="number> the flag of the team && name of team and value -->
    <div class="p-2 mb-2 border w-100">
        <div class="d-flex justify-content-around">

            <div>
                <img src="<?php echo $flags[$teams[0]]; ?>.png" alt="image">
                <span> <?php echo $teams[0]; ?> </span>
            </div>

            <div class="input-group d-flex p-3  bg-secondary " id="bgInput">
<!-- type hidden for storage the $keys in matchName -->
            <input type="hidden" name="matchName" class="form-control" value="<?php echo $key; ?>"> 
                <input type="hidden" name="<?php echo $key ?>[]" class="form-control" value="<?php echo $teams[0]; ?>" > 
                <input type="number" <?php if($value["STATUS"] == true) { echo "readonly";} ?> 
                name="<?php echo $key ?>[]" class="form-control" 
                value="<?php echo $values[0]; ?>" min="0">

                <input type="hidden" name="<?php echo $key ?>[]" class="form-control" value="<?php echo $teams[1]; ?>"> 
                <input type="number" <?php if($value["STATUS"] == true) { echo "readonly";} ?> 
                name="<?php echo $key ?>[]" class="form-control" 
                value="<?php echo $values[1]; ?>" min="0">

            </div>

            <div>
                <span> <?php echo $teams[1]; ?> </span>
                <img src="<?php echo $flags[$teams[1]]; ?>.png" alt="image">
            </div>

        </div>
    </div>

<?php

}

?>
<!-- <input type="hidden" name="RESET" value="RESET"> -->
    <button type="submit" class="btn btn-warning container" name="_method" value="PUT">Submit</button>
    <input type="submit" class="btn btn-danger mt-1 container" name="RESET" value="RESET">
</form>
</section>

<section class="col-md-6">
<table class="table ">
  <thead>
    <tr >
      <th scope="col">#</th>
      <th scope="col">Selecion</th>
      <th scope="col">PTS.</th>
      <th scope="col">PAR.</th>
      <th scope="col">GAN.</th>
      <th scope="col">EMP.</th>
      <th scope="col">PER.</th>
      <th scope="col">G.F.</th>
      <th scope="col">G.C.</th>
      <th scope="col">+/-</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if( isset($_REQUEST['_method'])  == "PUT"){

    foreach (clasificar(resultadosPartidos($pardidos))  as $key => $value ){
    ?>
    <tr >
    <th> <?php echo $key + 1 ; ?> </th>
        <td><?php  echo $value["Team"];  ?></td>
        <td><?php  echo $value["POINTS"];  ?></td>
        <td><?php  echo $value["GAMES_PLAYED"];  ?></td>
        <td><?php echo  $value["GAMES_WON"];  ?></td>
        <td><?php echo  $value["GAMES_EQUAL"];  ?></td>
        <td><?php echo  $value["GAME_LOSTS"];  ?></td>
        <td><?php echo  $value["GOALS_SCORED"];  ?></td>
        <td><?php  echo $value["GOALS_RECEIVED"];  ?></td>
        <td><?php  echo $value["DIFF"];  ?></td>
    </tr>
    <?php 
    }
} else {
    echo "
<tr>
    <th>1</th>
    <td>BELGIUM</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
</tr>
<tr>
    <th>2</th>
    <td>CROATIA</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
</tr>
<tr>
    <th>3</th>
    <td>MOROCCO</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
</tr>
<tr>
    <th>4</th>
    <td>CANADA</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
    <td>0</td>
</tr>";
}
?>

</tbody>
</table>
</section>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
