<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<? require_once( '_framework.php' ); ?>
<? $standAlone = getBooleanParam( 'standAlone' ); ?>

<head>
<title>World Cube Association - Official Results</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="Stefan Pochmann, Josef Jelinek" />
<meta name="description" content="Official World Cube Association Competition Results" />
<meta name="keywords" content="rubik's cube,puzzles,competition,official results,statistics,WCA" />
<link rel="shortcut icon" href="images/wca.ico" />
<link rel="stylesheet" type="text/css" href="<?= pathToRoot() ?>style/general.css" />
<link rel="stylesheet" type="text/css" href="<?= pathToRoot() ?>style/pageMenu.css" />
<link rel="stylesheet" type="text/css" href="<?= pathToRoot() ?>style/tables.css" />
<link rel="stylesheet" type="text/css" href="<?= pathToRoot() ?>style/links.css" />

<? if( $mapHeaderRequire ){ ?>
  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAGU1lxRKjKY2msINWGWVpGBQbYy8YqffdsRVCI9c6jAKj6rG0nxSHbmoN9OgZk4LBxdzm88fVVb-Ncg" type="text/javascript"></script>
  <script type="text/javascript">

    var center;
    var map;

    function load() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.enableScrollWheelZoom();
<?
if( $chosenRegionId && $chosenRegionId != 'World' ){ 

  $continent = dbQuery("SELECT * FROM Continents WHERE id='$chosenRegionId' ");
  
  if( count( $continent ))
    $coords = $continent[0];
  else {
    $country = dbQuery("SELECT * FROM Countries WHERE id='$chosenRegionId' ");
    if( count( $country ))
      $coords = $country[0];
    //else ERROR !

  }
  $coords['latitude'] /= 1000000;
  $coords['longitude'] /= 1000000;
  echo "map.setCenter(new GLatLng($coords[latitude], $coords[longitude]), $coords[zoom]);";
}

else
  echo "map.setCenter(new GLatLng(20, 8), 2);";

for( $i = 1; $i < 10; $i++ ){
  echo "var blueIcon$i = new GIcon(G_DEFAULT_ICON);\n";
  echo "blueIcon$i.image = \"images/blue-dot$i.png\";\n";
  echo "markerBlue$i = { icon:blueIcon$i };\n";

  echo "var violetIcon$i = new GIcon(G_DEFAULT_ICON);\n";
  echo "violetIcon$i.image = \"images/violet-dot$i.png\";\n";
  echo "markerViolet$i = { icon:violetIcon$i };\n";
}

echo "var blueIconp = new GIcon(G_DEFAULT_ICON);\n";
echo "blueIconp.image = \"images/blue-dotp.png\";\n";
echo "markerBluep = { icon:blueIconp };\n";

echo "var violetIconp = new GIcon(G_DEFAULT_ICON);\n";
echo "violetIconp.image = \"images/violet-dotp.png\";\n";
echo "markerVioletp = { icon:violetIconp };\n";


  $isFirst = true;
  $countCompetitions = 0;
  foreach( $chosenCompetitions as $competition ){
    extract( $competition );

    if( $latitude != 0 or $longitude != 0){
      if( $isFirst ){
        $previousLatitude = $latitude;
        $previousLongitude = $longitude;
        $isFirst = false;
      }

      //echo "$countCompetitions";

      if( $latitude != $previousLatitude || $longitude != $previousLongitude ){
        $previousLatitude /= 1000000;
        $previousLongitude /= 1000000;

        $infosHtml .= $pastVenue;
        echo "marker.bindInfoWindowHtml(\"$infosHtml\");\n";
        echo "map.addOverlay(marker);\n";

        $previousLatitude = $latitude;
        $previousLongitude = $longitude;

        $countCompetitions = 0;
        $infosHtml = "";

      }

      $infosHtml .= "<b>" . competitionLink( $id, $cellName ) . "</b> (" . competitionDate( $competition ) . ", $year)<br/>";
      $pastVenue = processLinks( htmlEntities( $venue , ENT_QUOTES ));

      $latitude /= 1000000;
      $longitude /= 1000000;
      $countCompetitions++;
      $cc = $countCompetitions;
      if( $cc > 9 ) $cc = 'p';
      echo "var point = new GLatLng($latitude, $longitude);\n";
      if( date( 'Ymd' ) > (10000*$year + 100*$month + $day) )
        echo "var marker = new GMarker(point, markerBlue$cc);\n";
      else
        echo "var marker = new GMarker(point, markerViolet$cc);\n";
    }
  }
  $previousLatitude /= 1000000;
  $previousLongitude /= 1000000;

  $infosHtml .= $pastVenue;
  echo "marker.bindInfoWindowHtml(\"$infosHtml\");\n";
  echo "map.addOverlay(marker);\n";

?>
      }
    }

    </script>
<? } ?>
</head>
<? if( $mapHeaderRequire ){ ?> <body onload="load()" onunload="GUnload()">
<? } else { ?> <body> <? } ?>
<? if( ! $standAlone ){ ?>
<div id="main">
<div id="content">

<?
  $sections = array(
    array( 'Home',         '../index'     ),
    array( 'Rankings',     'events'       ),
    array( 'Records',      'regions'      ),
    array( 'Competitions', 'competitions' ),
    array( 'Persons',      'persons'      ),
    array( 'Multimedia',   'media'        ),
    array( 'Statistics',   'statistics'   ),
    array( 'Misc',         'misc'         ),
  );
?>

<div id="pageMenuFrame">
  <div id="pageMenu">
    <table summary="This table gives other relevant links" cellspacing="0" cellpadding="0"><tr>
<? foreach( $sections as $section ){
    $name   = $section[0];
    $id     = $section[1];
    $active = ($id == $currentSection) ? 'id="activePage"' : ''; ?>
<td><div class="item"><a href="<?= pathToRoot() . $id ?>.php" <?= $active ?>><?= $name ?></a></div></td>
<? } ?>
    </tr></table>
  </div>
</div>

<div id='header'>World Cube Association<br />Official Results</div>
<? } ?>

<? startTimer() ?>
