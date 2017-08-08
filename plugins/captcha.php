<?php


// PHP Compiler by Serge Igitov (Dagdamor), Version 2.4.5, Copyright 2008
// Released under the LGPL License (www.gnu.org/copyleft/lesser.html)

// PHPC Online Plugin - CAPTCHA Support v1.1 by Dagdamor

define("CaptchaTimeout",300);
define("CaptchaRetriesLimit",5);
define("CaptchaImageNoise",0.3);
define("CaptchaImageSqueeze",1.2);
define("CaptchaImageDistort",0.2);



/****************************** Class Definition ******************************/

class CaptchaSupport
{
  function generateImage($width, $height, $bgcolor, $code)
  {
    if(preg_match("{[\da-f]{6}}i",$bgcolor,$matches)) {
      $bgcolor=array();
      $bgcolor[]=hexdec(substr($matches[0],0,2));
      $bgcolor[]=hexdec(substr($matches[0],2,2));
      $bgcolor[]=hexdec(substr($matches[0],4,2));
    }
    else $bgcolor=array(255,255,255);
    $image=imagecreatetruecolor($width,$height);
    $color=imagecolorallocate($image,$bgcolor[0],$bgcolor[1],$bgcolor[2]);
    imagefilledrectangle($image,0,0,$width,$height,$color);
    $colors=array();
    for($index=0; $index<4; $index++) {
      $coeff=(random(100)-50)/1000+CaptchaImageNoise;
      $red=round($bgcolor[0]*(1-($index/3)*$coeff));
      $green=round($bgcolor[1]*(1-($index/3)*$coeff));
      $blue=round($bgcolor[2]*(1-($index/3)*$coeff));
      $colors[]=imagecolorallocate($image,$red,$green,$blue);
    }
    for($y=0; $y<$height; $y++) for($x=0; $x<$width; $x++) if(random(2)) {
      $color=random(count($colors));
      imagesetpixel($image,$x,$y,$colors[$color]);
    }
    $symbols=$this->getSymbols();
    for($index=0; $index<strlen($code); $index++) {
      $char=$code[$index];
      if(!isset($symbols[$char])) continue;
      $symbol=imagecreatefromstring(base64_decode($symbols[$char]));
      $x=($index-strlen($code)/2+0.5)/CaptchaImageSqueeze*40;
      $x=round($width/2+(rand(0,100)/100-0.5)*CaptchaImageDistort*40+$x)-20;
      $y=round($height/2+(rand(0,100)/100-0.5)*CaptchaImageDistort*40)-20;
      imagecopy($image,$symbol,$x,$y,0,0,40,40);
    }
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: image/jpeg");
    imagejpeg($image);
  }

  function processImage()
  {
    global $database;
    if(isset($_GET[PhpcSessionCookie]) || isset($_POST[PhpcSessionCookie])) exit;
    $hash=acceptStringParameter(PhpcSessionCookie,32);
    if(!preg_match("{^[\da-f]{32}\$}",$hash)) exit;
    $ipaddress=$_SERVER["REMOTE_ADDR"];
    $minimalTime=phpctime()-PhpcSessionTimeout;
    $conditions="hash=".slashes($hash)." AND ipaddress=".slashes($ipaddress)." AND lastactivity>=$minimalTime";
    if(!$database->isLinePresent("sessions",$conditions)) exit;
    $globalCache=$database->getLine("settings","groupid=0 AND name='globalCache'");
    $settings=array();
    eval($globalCache["value"]);
    $minimalTime=phpctime()-CaptchaTimeout;
    $conditions="hash=".slashes($hash)." AND ipaddress=".slashes($ipaddress)." AND dateline>=$minimalTime AND counter<".CaptchaRetriesLimit;
    $captcha=$database->getLine("captcha",$conditions);
    if(!$captcha) {
      $code=generatePassword($settings["captchaCodeLength"],true,false);
      $captcha=array(
        "hash"=>$hash,
        "code"=>str_replace("0","O",$code),
        "ipaddress"=>$ipaddress,
        "dateline"=>phpctime(),
        "counter"=>1);
      $database->deleteLines("captcha","dateline<$minimalTime");
      $database->addLine("captcha",$captcha,true);
    }
    else $database->incrementField("captcha","counter","hash=".slashes($hash));
    $width=$settings["captchaImageWidth"];
    $height=$settings["captchaImageHeight"];
    $bgcolor=$settings["captchaImageColor"];
    $this->generateImage($width,$height,$bgcolor,$captcha["code"]);
  }

  function processCheck($currentSession, $field)
  {
	session_start();
	if($_REQUEST['captcha'] == $_SESSION['capcha']){
		return true;
		}
  }

  function getSymbols()
  {
    return array(
      'A'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACP0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWCg1gJGRURNIceFR8gKYEZ+Saz5AAFEUgkDHsTPgdxwICFJiB0AAURrFIkSoYQd6hGxHAgQQpQ4k1mI+ci0ACCCyHQgMFZCl7EQqFwSqZybHHoAAoiQESYk2ZhLVwwFAANHLgSBAVjQDBBBZDgRGFyhz4IqyNzjEBaG5niQAEEDkhiCu0HgPxK/x6CM5mgECiGQHQkMBl0WfgIXyNyD9E4c8McUSCgAIIHJCEF8ovEej0QGoTCRUsKMAgACipgPfA0PvL4xNhn6sACCASHIg1Pe4QuATjEEgmkVJsRMggEgNQWKiFxcfBphJqfoAAohaDnyDFL0wgC+aiS4TAQKIaAdCfY2rHPuELkAoNxNb9QEEECntQXy+BuVOCSzi6KGKDEAexlWowwFAABHlQKhv8ZVh0sSYgwZAmYWgAwECiNgopqjRiQNwEVP1AQTQQDqQKHMBAoigA6G+JLvBSQAQrPoAAoiYNIjPl6A0hCunojsEW3SCqz5ojscKAAKIGAfi8uVfoMEPidAPigUQhSsjgTILTnMAAghvFEOrNlwJGV9BTIpavOkQIIAIpUF89SZG4YwLAEMalAxwRSMztAGMFQAEECEH4vIdKHpJCUEQIKvqAwggnA6EVm24qiNSHUdID85eH0AAMQ728UGAABr0g0cAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAAQYA6AZRderyIvsAAAAASUVORK5CYII=",
      'B'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACEklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjIKAGkpMm04xMQ/wLRwAz5nlTNAAFElAMpBHxQWgTo0Z9A+ikpDgUIIHpHMTsQKwEdKkKsBoAAGqg0KA90JDsxCgECaCAziSAxigACiBppEJSmXiALQEMH5AB8GYsXiF/gkQcDgACiSQgCHfwT6miCDiAEAAKI1lH8GY/cX2IMAAggWjuQGY/cJ2IMAAggWjsQV0YAlYdElYUAAUSNTMIOzBR8aGJcQAwq67AVJaCovQdMo0RFMUAAUcOBIlBMDPgGxA+BjvtGrOEAAUTvcpDkuhgggOjtQFC5qAlMEqDqDl8GggOAABqomgSUedSIcSRAAFEjDYKKC2zlHSiD8DJgzyggAMpISkB8G5/hAAFEDQd+Rq/qkAEwlEDRKoFDmg9ULYJqHlz6AQKI5lEMtPwpA6TcwwXwNhoAAoheaRCfA/ECgACilwPJTkoAAURzB0JrGS48SvCGLkAA0axPAnQYyFGg9IUrg8AA3kYDQABRw4HS0JxKDnhBqE4GCKCBbPKD6mOCDVqAABooB4Icd4uYFg1AANGjX4wMQA56ga9gRwcAAUQPB8JGFr4C8Xti24EwABBAjIN9fBAggAb94BFAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAgAEATQxjnPpTM7YAAAAASUVORK5CYII=",
      'C'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAChklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIWahjEyMvIBKTYs5n4D4j/A9P6NVDMBAohiBwIdJQKkQA4TJELtXyD1CYSBjn1DjPkAAcRIbi4GWgZykDQQs5NlAAMDyLEvgPa/wKcIIIDISoNAx8kDKSUG8h0HAswMEA/iBQABRHIUAx2nygCJUroAgAAiyYFAx4F8TKzjPiGxyfYQQAAR7UBoDpUgoOwnAyRdYWQAaJqFZSiiAUAAkRKC4gTk3wAd9hCXJFDuPZB6D/WoHAOR6RcggIjKJEBDuRjw+/wTPschA6A6UNRfB+L3xKgHCCBiQ5BQGfeISHPAAOhIUBFzDxqaeAFAABFbzOAzCBS1P4k0BwVAQxMvAAggYh3IhUeOoCWUAIAAIuhAaPrDB0iuX0kBAAFETAjiTafkRi+xACCABn1zCyCABr0DAQKIGAf+wScJTKOUNBgIAoAAIuhAIhqZhDIRRQAggIiNYnwZgaYtG4AAItaBn/HIiZAbzUQUYQwAAUSsAwkVxgQbnugA2ujVJKQOIICIciC0JYIvmgWhFhLjMD4gBjlMhBj1AAFESnPrKQOkmY8LgKKaF0iD+hjvoQ0CZIeBGhx8xDoMBgACiKROE9ASkAMJ9t6gANzVhLJxZiSg/WfxGQIQQKT2SUBtPlCGIKZooUrxAxBAJNUk0Gi7xUDjFgwyAAggSvrFoP4JCDOTaTfO/gsyAAggsh0I1szICHKcKAMkXRITpSBHgcrU98Q0VkEAIIAociCKQRDHcjNgdygow/wkp2kGEEBUcyCtAEAADfrmFkAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIEGAB/PI5htldftQAAAABJRU5ErkJggg==",
      'D'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACBElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWAgpYGRklABS0iSa+w2I/wDxZyD+CcSfgPgvPg24MitAABF0IJmAC0rzIYm9AeIXDBAHEw0AAoieUSwCxDoMJMYGQAANRBoEJRlNIGYmRjFAAA1UJgElATUGIhwJEECUpsGnwMT9AlkAmKn4oA4QZECkRWwAJCcDxA/xWQAQQFQPQaCDP4EcDcTXgdx7DPhzLyhd8uGRZwAIIJpGMdCR74HULQb8jhTHZwZAANEjDYLKxBd45GFJAisACCB6ZRKQA/GFIs5oBgggeubi93jkeHFJAAQQPR2IrwZhxyUBEED0dOA3PHI4HQgQQIO+NQMQQIPegQABRE8HsuGRw5k+AQKIng7kxiOHM30CBBA9HYizKGHA40CAAKKXA0FNLJw5lQFPGQkQQPRwIKgak8AjD3IczjQIEEC0avKDAbDpBWpyyTPgb/c9xWcGQABR3YFAR4FCDNYexNuUYoA4Dm8fBSCAKHWgNNBBpPb4YADWicILAAJooApqkOPwtqRhACCAaJoGcQBQtBIMORgACCB6ORDUFgTlVpL7xQABRCsHfoLSsJEFfG1BvAAggBgH+/ggQAAN+tYMQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQIABAKJ3SgUHbvADAAAAAElFTkSuQmCC",
      'E'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABOklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhVyMjIKAGkpKlk7ydg5rxNjEKAABr0IQgQQIPegQABNOgdCBBAg96BAAFEdCYhAN4A8XsS1P8hViFAAFHLgT+BufITlcxCAQABNOijGCCABr0DAQJo0DsQIIColQalgTUNsbUM0bUICAAE0KAPQYAAGvQOBAigQe9AgAAa9A4ECKCBqEmIrkVAACCABn1NAhBAgz6KAQJo0DsQIIAGvQMBAmjQOxAggKiVSdiBVR0fCer/ADPVN2IUAgQQtRwoAsXEAlCOJ6o+BgigQR/FAAE06B0IEECD3oEAATToHQgQQIyDfXwQIIAGfQgCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBBgADO0m1qrFf7sAAAAASUVORK5CYII=",
      'F'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABN0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWEhRzMjIKAGkpKlk9ydgBr1NSBFAAA36EAQIoEHvQIAAGvQOBAigQe9AgAAiKZMQAG+A+D0J6v8QowgggKjpwJ/AXPmJiuaBAUAADfooBgigQe9AgAAa9A4ECCBqpkERYE3DS6Tab8D0+pQYhQABRE0HskMxVQFAAA36KAYIoEHvQIAAGvQOBAggaqZBUCH9mUi1RNUiIAAQQNR04GdgznxBRfPAACCABn0UAwTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAADEO9vFBgAAa9CEIEECD3oEAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEGAAxScf2RZI5DgAAAAASUVORK5CYII=",
      'G'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACkElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIUahjAyMrIDKRDmwiL9B4h/AfFXYHr/S6rZAAFEtgOhjhIBYkGo44jR8xNIfQbiT0DHvidGD0AAMZKai6EOk4Y6jBIAcuwbIH6NL2QBAogkBwIdBwoxGSBmptBxyOA20A2fcEkCBBDRUQx1nDxVnEQCAAggohxIguNAUfWVAZLOYIALiolKp+gAIIAIOhCa5mQIKAOlpxfAqHqDxxyQI0UZIBmLaAAQQMSEoAQD/jQHctQTQkUIUP4bkHoIdOhrBkhsYCuSMABAAOEtqJGKElwAVFw8JKV8AzkUiK8DmS+IUQ8QQIRCkFB0PCLKVVgA0JFPgQEAKgt/4lMHEECEHMiHR+4N0BK8hhMC0GjHCwACiFBdjC+d4Cy7qAkAAginA6G5Dh8g6HtqAIAAwhfFeKMfW/QCPQVKEqokugFvTQIQQIO+uQUQQIPegQABhM+Bf/BpJCKNUgUABBBOBxJRBNDFgQABRKgc/IbHIaAMgVL3QhP7WXSFwNBWZcBfpuIEAAFEKA3iK+sE6RHNAAFEyIGEmuXyQEdSs/GKAQACCK8DoekQXyiCQlCNlo4ECCBiipmnBORBjtQFOlKCFg4FCCCC7UFQKAItBjlSGo8yZqi8NFAtKNRBGLmmIas1DQIAAURUkx/oyBdEtA1hANbEpwoACCCiaxJQw5SBcHRTHQAEEEkdd2hIgqJPnIHMcg0JgFrhBBusAAFEcscdrhHSchGEYmIzB6hEAKdRYkcWAAKIbAeiGIJ/bAbkoD/EtJ6xAYAAoooDaQkAAmjQN7cAAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIMAARROoPGTkcs4AAAAASUVORK5CYII=",
      'H'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABIUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWAgpYGRklABS0jiknwLxCyLswWsGMKPiNAMggAZ9CAIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgQQwZqEAJBmwF1DUAUABNCgD0GAABr0DgQIoEHvQIAAGvQOBAggSjPJGyB+T4Q6QSAWIccCgACi1IE/gfgTEeq4yLUAIIAGfRQDBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAAMQ728UGAABr0IQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAATToHQgQYAB6MxF8AbJ9pQAAAABJRU5ErkJggg==",
      'I'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABEklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWEjVwMjIqAqk+Ciw8zYwY34iVjFAAA36EAQIoEHvQIAAGvQOBAigQe9AgABipLQmAWYaUIZRxSH9CWj+bUrMBwigQR+CAAE06B0IEECD3oEAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAATToHQgQQCQ3WOndqwMIoEEfggABNOgdCBBAg96BAAE06B0IEEAU9+poDQACaNCHIEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IECAAQD6ARtDiUe2pgAAAABJRU5ErkJggg==",
      'J'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABg0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWMjVyMjIqAqk+EjU9hSYKV+QogEggAZ9CAIE0KB3IEAADXoHAgTQoHcgQACRnUmAif02NnFg5pEAUtJkuwgNAATQoA9BgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgACihQO5qGkYQABR1YHAphYzA+n9FLwAIICIdiDQcnZQWw/qCGzyIHElIMYqDwWfSHQfA0AAkdJgZWeANESlgY4BWfQZSQ4UrXwEHPcT2Mj9RqoDAQKI3BY1HwMZXU5yLAIIIHrl4jfA0HtPjkaAACK7T0ICILmzjgwAAohWDvwLxKAQewF03E9KDAIIIEZSxgdBOZkBklmYoTQ6+APEv4BmkpxbcQGAACLJgQMBAAJo0Fd1AAE06B0IEECD3oEAATToHQgQQIPegQABNOgdCBBgADxIMl70S30UAAAAAElFTkSuQmCC",
      'K'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACNElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjIKAGkpHFIPwVmtBcE9IP0SuBR8gaIH2LLsAABRPMQBDpOkAG/4z4B8UNckgABRFMHAh3HBaTk8Sj5BsT38JkBEEA0cyDQccwMEMcx41DylwEScn/xmQMQQLQMQRkg5sIjfw+Y5r4RMgQggGjiQGimEsGjBJQhPhFjFkAAUd2B0HSHK8eDwBug494Qax5AAFHVgdB0p4ZHySeg43DmWGwAIICoHYJKDLgzBcEciw0ABBDVHAgtjPlwSINzLDD08OZYbAAggKjiQKDjQBkCX2FMVI7FBgACiKiqjgAAZQpcIQcCROdYbAAggKjhQEE8cp9IybHYAEAA0bou5oPWxWQDgACiR3NLHuhIdnI1AwQQPRwIKnaUyNUMEEDUciAoh77HI88FLYZIBgABRI1MAirbbsEcAsS4olMC6MhvwEyDzyMYACCAqBGCL0AFMLQQfkRArTy0riYaAAQQVdMgtLx7ikcJuI0IrbOJAgABRPVMAu2f4CuYQSEoQ6x5AAFEq1wMahTgq3dFoNUjQQAQQDRxIDQ9Emq5yBCTHgECiGblILHpkQF38wwMAAKIpgU1ND3ia8UQTI8AAUSPmoRgemTA038BCCCaOxAYij8Z8HTMoQBnDxAggBgH+/ggQAAN+sEjgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAADAC7Bd1kgClLsAAAAAElFTkSuQmCC",
      'L'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABGUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWEhRzMjIKAGkpHFIPwVmuBeUOwkVAATQoA9BgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECCCSOk0EADuwU8VHgvo/wE7WN0KKAAKImg4UgWJiwScgvk1IEUAADfooBgigQe9AgAAa9A4ECKBB70CAAGIc7OODAAE06EMQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIMAA98QSS2bW0HUAAAAASUVORK5CYII=",
      'M'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACBUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWLAJMjIySgApaTz63gMz1z1iLQGapwOk2PEoeQo07wU2CYAAIjcEBYGW4rMQDoDqBBnwOw4vAAggSqJYgkh1IhTYwQAQQJQ4EBSKzPgUQEOZjwI7GAACiBIHghwnSkANsaGMEwAEEKW5GGf0QUNXkELzGQACiFIHsgMdgsuRoNDFmwSIAQABRI1yEFcoUZQ5YAAggKjhQD5gKHIhC0BDleyiBRkABBA5DvyERQw9s2ALVWz6CAKAACLHge+xiInACm5oaKIXLT+B+DMZdjEABBDJDgRWSW+A1F8sUrBQw1b0vCHVHhgACCBy0yC2elMCGoromQPkmddk2sMAEEDkOhBbNIOKFCVsaoGhji3EiQIAAUSWA4EWgtIUNkdyYREjO/RAACCAKClmsDkQHXwDeuYbBXYwAAQQ2Q4EWgxy4E8CyigKPRAACCBKC2p8ufMvNMdTBAACiFIH4gshikMPBAACiCIHQnMnrlCiOPRAACCAsPZJSASgkGJDE/sGzekUA4AAotiB0Fx6mwpuwQoAAmjQdzsBAmjQOxAggAa9AwECaNA7ECCABr0DAQKIcbCPDwIE0KAPQYAAGvQOBAigQe9AgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAAwADDU+P+//mwAAAAABJRU5ErkJggg==",
      'N'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAB2ElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWPBJMjIySgApaTxK/gLxZWBG+0umOZ+Aem/j0wsQQJSGIDMQy1BoBl4AEEDUiGIRYAjxUcEcrAAggKiVBuWAjmSmklkoACCAqOVAdiAWpZJZKAAggKiZi6WBochFRfPAACCAqF3MyFPZPAaAAKK2A7mgRQrVAEAAketAfOWeBNCR7GSaiwEAAohcB34F4jc45EC5WY5MczEAQABREsVPGHCHJB8wFAUpMBsOAAKIbAdCq7cneJTIU6NsBAggijIJ0JGgaP6EQ5oq1SBAAFEjFz/FI0dxNQgQQBQ7EBiK3xjwO5KiDAMQQNQqB18D8U8ccqAih+yyESCAqOJAaIbBF4pkZxaAAKJaTQJ05Hsg9Z5a5sEAQABRu6oDhSLe1jWpACCAqOpAYCiC0uELapoJEEBU7zQBHQly4DdqmQcQQLTq1eHLMCQBgACiiQOBoQiqXagS1QABRMt+MciBFGcYgACimQOJaEwQBQACiKYjCwQaE0QBgACix9DHI0o0AwQQ42AfHwQIoEE/eAQQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAATToHQgQYAAkl1DzjpZrywAAAABJRU5ErkJggg==",
      'O'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACnklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIVSAxgZGfmAFBsWs74B8U8oJghw5QWAACLZgUAHMQMpQSjmI0ILyIGfgfgFA5GORQYAAcRISi4GOg7kKHkgZibVIih4A8RPgPgvugQudwAEENEOBDoO5DARMh2GDECOu8UASQJwgMsdAAFEVCYBOk6VgTqOAwFQ6KsBMRcxigECiGAIAh0nDaQkCJgDS2ewNMbLQDh9gkLyOkwPLncABBBeB0JzqCoBS0Bp6g0WOXYgBnlOEI/+T0B8G58DAQKIUBSLE3DcLRyOAwFQyNzDIw8CfAwEQhoggHA6EBp6+DQ/Afr6Gx55uDoG/MUL3rQNEED4QpAXj9xPoOPwhQwyAIX0CzzyoCSAs9gCCCB8DsQXeu8JOIpU9TjtAgggfA7EVwx8JmAhOgCF4ic88uy4JAACCKsDgemPUBlFcpXFgFYwowGc9gEEEK4QxFtHA9MfOQ7EqN6QAM40CBBAg765BRBAg96BAAGEy4F/8GkCplGciRoPwNcCwhn9AAGE1YFEFMDkOBBfxsNpH0AA4YtifI7EV4hjA6DQw1eu4sx0AAGEz4H4yi18DQBy1OO0CyCA8DkQX+nPDkyHhJpgMAAKPXxqQfbgTIMAAYTTgdB0iC8UJYgo0EFAhgF/msVbpwMEEKFi5iUeOVjLGFdrBOQoJTzyIPCJAX8gMAAE0KBvUQMEEFGdJip2mJAdh9JxwuUOgAAiqiYBan7IQCCtkACw9upwAYAAIqdfDIpycgpqECC5XwwQQCQ5EK6JkREU3VQdWcDlDoAAIsuBKAbQeGwGIIAodiCtAUAADfrmFkAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIEGABiZ6ky9W4bTwAAAABJRU5ErkJggg==",
      'P'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABvklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjIKAGkpMkw/xsQ/4TS74EZ8iepBgAEECMxuZgCB6KDN0D8BGjnX2I1AAQQvaNYBIh1gR7mIlYDQAANRBpkBmI1oCOZiVEMEEADlUlAjpMhRiFAABGVSQiAp8A09QJZABg6gkAKhnEBEaA6gukRIIBoEoJAS0E59h6Q+ZSAUj5CZgEEEE2jGBqy+EKInZAZAAFEjzT4lRLNAAE06GsSgACihwPxpbNvhDQDBBBNHQitgfABgtEPEEDUKGYwALQQFmXAXz2+J6bKAwggajgQVJ7xIvHZGYjInQyQepkgAAggajiQWAchgxfA0PtEjEKAABqIXPwG6DhCBTgcAAQQTdIgDgBKbw9BtQwpmgACiB4OBDdWgfg1Ke1AGAAIIGo48A3UAejgD9BBBMs5QgAggKjhwJ/EJnhyAEAADfqqDiCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCAiBpZGEgAEECDPgQBAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAgwAWitayuo1b9QAAAAASUVORK5CYII=",
      'Q'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAADEElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIVSAxgZGfmAFBsWs74B8U9gGv9JifkAAUSyA4EOYgZSglDMR4R6kAM/A/ELchwLEECMpORioGUgR8kDMTOpFkHBGyB+ArTzL7EaAAKIaAcCHQdymAiZDkMGIMfdAtr7jRjFAAFEVCahouNAABT6akAzuYhRDBBABEMQaJA0kJIgYA4sncHSGC8D4fQJCsnrhNIlQADhdSA0h6oSsASUpt5g0csOpECeE8Sj/xNQ7218DgQIIEJRLE7AcbewOQ4EQCEDxPcYIBkDF+CDBgJOABBAOB0ITSP4ND8hMqE/YUBEPTaAN20DBBC+EMQXNT9xhRw6gBYpL/DZAy1bsQKAAMLnQHyh956Qw0hUj9MugADC50B8xcBnAhaiAGgofsKjhB2XBEAAYXUgEWUUOfUrvvSK0z6AAMIVgnjraDIbAPiqN5xpECCABn1zCyCABr0DAQIIlwP/4NMErSVIBfhaQDijHyCAsDqQiAKYHAfiy3g47QMIIHxRjC8j8BJ0DhKAFsT4ylWcdgEEED4H4ivr8NUy5KjHWUYCBBA+B+Ir/dmBoUKoCQYG0NDDp/Y9vhY2QADhdCBQE8hX+KJZgshGpwwD/jSLt04HCCBCxcwjPHKwljHW1ggopwOxEgP+1sonaEDgBAABREyLmpjmPs1a1AABREy3E9Se42LAX0ywM5Be9DwkpsoECCCienXQhA4KSVJzLzYACrl7hKIWBgACiNR+MSg3gjC5/WJQyfCUlMYGQACR5ECwBsTIgigD/mhHd9gLYvvCyAAggEh2IIpmiGO5GSAOBaVBXJkJFKWktsLBACCAKGrNgApYUFoCYlCfA2+/g1w7AAKIas0taLrCVSOAOkZKyAU7tJwUQRdHBwABRPHwGxoARSOuaBaEOhSbHM7aBCCAqN1gBUUz0SNXxACAAKKqA6HRfIuBio4ECCCqN/mhRQnIkUQVxIQAQABRVMwQNBzSNQClPRDNhib9iwFSd4M89BVXkwsgwAD2WepOp3H2EwAAAABJRU5ErkJggg==",
      'R'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACG0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjIKAGkpMm04xMQ/wXib0D8Gpgp/5KiGSCAiHIghYAPSgsCsQTQsy+AjnxBrGaAAKJ3FDMDsTTQkZpAzEyMBoAAGqg0yAXE8sQoBAiggcwkgsBQ5CKkCCCAqJEGn6KnKWj0gdKcKAMktHABkJpv+AwHCCCahCAopwLxGyDzFhD/xKOUYAgCBBBNoxhapLynxAyAAKJHGiSp3EMHAAFEDwfii8bPhDQDBBBNHQjNpYJ4lOBLn2AAEEA0qUmADgPVHiDHSeBR9g2YRgmmT4AAooYDQTUDqfU0KF0+JEYhQAANREENctwtYOjhLf9gACCA6NFYQAagls1TYh0HAgABRE8HgtLcbVI1AQQQPaOYC5hWRUjVBBBAVK2LoXWwLgOkWYUNyADVfCMligECiKohCK3a8OVOkMOViG0LggBAAFE9iqFlG74WMzsDkW1BEAAIIFq1Zp4y4G9GCUL7OQQBQADRMpPcY8DfUJCG1jh4AUAA0cyBwFAE1bOEaguC6REggGjdHgSlxzd4lIAcp4bPDIAAokc5+IQBf3rkwleXAwQQzR2IVPTgS48SuApxgACiS00CLZifEFAmg62XBxBAjIN9fBAggAb94BFAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAgAEA+ox1g+CaVdwAAAAASUVORK5CYII=",
      'S'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACwUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIWQAkZGRmLM4QJidihGB9+A+A+UxgCE8gBAABF0IB7AB8SCUMxMpJ5PQPwZSmN1MDoACCBGQj7AEoKg0JKGOpAS8BOIrxCyHyCASA1BESCWJ9dFaABbcsAAAAFESiahpuOIBgABRGwIgnwrQ0ANKMo+Q2kY4GJAZCCyAEAAEetAUOjhygh/gfgeAyTh4wIgBwpCzSHJsQABRKwD8WWIJwz4HQcCoFB9AcUgR4Jig6icDxBAxDqQC4/cLyLNgIE3QPweiEWJUQwQQNSoSUBFDrHlIAyAksULYhQCBBCx5aAOA/60A7IQFCqwAvgnHrUogJD9AAFEbBSDLMXnQFAIikAxCIAcDHMs0bUGNgAQQMSGICiTqJJrCQMihF8zoDmWkP0AAURKVafEACkqKAUghz5kgDiaoAMBAogUB4KiEeRISutgEAA57hYQfyNkP0AAkZKLQYbeBuKnUDYlAOZZgrkfIIDIac3ALACVY6Aox1dGEgJPgfbjLW4AAohcByIDkGNB0Q6rd7kZSGgfAu2/jU8BQABR0mCFAVgOfY8kBnIoyNGE6l6C6RkggGjVJwEVJaCou85AuJ7GCwACiBgHgnxpwEBeEQMK3Zdk6IMDgAAiNgRhuQ6ESW3bseGRIxi6AAFEahqEdZJABsPSHb4iBxT6+Bq6nwlZCBBA5GYSPigGdQFgjQPkKgzWisZXBIH0vCZkEUAAUSMXw4oXUtMoqBVOsMAHCCBqOJBUAOsiENXCAQggYhwIigpQWqNGQwGUdh8xkNBeBAggUhsLIEfC0h+xtQXMgyCMEWqE7AcIIEqqOthYDKgYwRYTsMyDN7QI2Q8QQAQdONAAIIAG/fAbQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgQYAF3jmpYU8fO1AAAAAElFTkSuQmCC",
      'T'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABLklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWPBJMjIy8gEpVRra/wmYSW/jUwAQQIM+BAECaNA7ECCABr0DAQJo0DsQIIAY8dUkwEzCDKS4CZghDcRcOOSeAvE3PHr/AO3HJ88AEEB4czFQ818g9QmfGqAnxPFIfwOagVc/IQAQQIM+igECaNA7ECCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCA8PbqBgMACKBBH4IAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAAQYArvIfNGaqkJsAAAAASUVORK5CYII=",
      'U'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABpklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWAgpYGRkNMYlB8xgZ4mxhBIzAAJo0IcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAAUeRAYEuZnRpq8AGAACLGgZ/wyPESoZ+LTLPBACCAiHHgLzxygkToFyHTbDAACCBiHPgejxwfMArlcUlC5fjINBsMAAKIYK8O2Ov6BLToGwPuqBIBygtCLfsJFQOlO5AYMx6jv4HMJmQ/QAAxEjM2A3QAyHGaBBWSBq4D7f5GSBFAABGVi6EGPaTYSQjwkBjHgQBAABEVgnDFkJBUYoBEITkAlATuEes4EAAIIJIcCNfEyAjKmaDET0wuBgFQ+vwEtOsNqXYBBBBZDkQxgJER5FA2IEbPcH+A+BcxGQEfAAggih1IawAQQIO+LgYIoEHvQIAAGvQOBAigQe9AgAAa9A4ECKBB70CAAAMAFwxNTfRhCRsAAAAASUVORK5CYII=",
      'V'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACP0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWHBJMDIyCgIpJRzSP4GZ6woxFgDNgTFFgFgeh7IXQPOeYpMACCCcIQjU8B5I/cUhzQ60mIsYByIBPjxy73FJAAQQoSjGqREIBAnoRQbMeNT/BOJvuDQCBBAhB77GI0eKA/GpfYNPI0AA4XUgMJpBPvuJQ5qUaCYrekEAIICIycWURjO+6MUXAGAAEEDEOBBfFBDjQHxq8CUhMAAIIIIOBEYzvkRMTDSTHb0gABBAxBbUZEUz0PH4ohdfMQYHAAFErAPxRYUoHjl80Usw9EAAIICIciAwmv/iMZAZWutgA7jE8ZmHAgACiJS6+BMeOYx0BnQ0OzZxKCDKcSAAEEBEOxAYiqDcjCvNYAspinIvDAAEEKmtGVKimayqDR0ABBC1HAgC8OiERi+u4gdv1YYOAAKIJAcCoxmUDnGV/II42OiA6PQHAgABRE6DlZhoJrtqQwcAAURNB4IAH4HoJSn0QAAggEh2ILSFgyuRCzJQKffCAEAAkdsnwRnNQCyNRw/Bqg0dAAQQtR2ID+Ar6HECgAAiy4HQFg4pjgSFHEnFCwwABBAl3U5SQoScEAcDgACixIGkpCmyHQgQQGQ7kEALBxmAkgNZ6Q8EAAKI0pEFYiwmO/RAACCAKHIgtHNPqGYgK3PAAEAAMQ728UGAABr0g0cAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAAQYAwxp1E0pjSsEAAAAASUVORK5CYII=",
      'W'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACzElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAGIFYHohFcMi/AWaih3gNYGTEp/8FUP9TCvQ/BAggUAh+xaOfC5/hUMCLR46PCP347PgMEEAgB77HpxnoQ2ZckkA5diDFToF+ZjwO/AYM/Z8AAcQEJP6COHgs4cYjhy/0YABfKOKT+wQiAAKICZmDA+CLAnyOJ0Y/PjlwoAEEEBMyBwfAF0qCeORgAF8o4XMgONAAAgjsQGA040uHWEMJmH5AhuNMX8iOwJMOcTn+EzTpMQAEEHI5iCuamaGOIdZwbAAjpIFm4tP/GcYACCAmbIJYADYHElMEwQC2WCCY/kAAIICICUFcFmBLf39x6MeWjnE58C8weuFuAQgguAOBgt/wWIBiGJ7oeQHEP7GIs0PLTGSAM/0hcwACCL0uxhWK6L7FWbgy4E4q8FAkkMFQShSAACLWgeihhrV4gUYNLjOQkwm+DIJSogAEELoDCWYUPNXTJzQaHSCnQ1zV409Q9YYsABBAKA6ESmJLQ3AHMuD2/WeoGbiqTuR0iKvwxwgggADC1h7EFYowh+FKf8ghh6vg5yXQwMBoWQEEEDYH4iuwQQZjC8G/0FIABnBVnaB0iK/8w/AYQACR4kAQEMRhAYoeaGbBVmTx4nHgN1j1hgwAAgjDgQSaX7havtg8hU0MFAO4GhhYAwYggHD1SXCFIq60gy3dkmoG1kABCCBcDsRX3KADjKKBDDNwtqgAAgirA5HrQiIAVocQKLLQAU77AAIIX7eTWEfiU4evnYkMcIY2QADhcyCxUYTPgfha6kSZARBAlIYg1qKBRDPQy1AUABBAOB1IoPlFlAOgjifkSLzyAAFEaOiDkOHERCGhpILXDIAAYhzs44MAATToB48AAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAgwAkkq7NtPHfYkAAAAASUVORK5CYII=",
      'X'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACjElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWHBJMDIyMgMpTSBmx6HkLxBfBmayv4QsAZolAaSk8Si5DjTnGzYJgADCGYJQix/hMRTkAXkiHMdFwHFPcTkOBAACCG8UAzV+AhmAR4kg0AGCeBwH8oQSHv2fgHa8wOcGgAAimAahBuD0IRDIQx2CDYCiFl8SuUfIfoAAIjaTPMQjhzWqgY7mY4A4EKeZxKRfgAAiyoHQNEJ0VENDVA6P+jdAM98TYzdAABFdzECj+hMeJchRjS9qfwLxE2LtBQggUstBUK7GFS3gqCYiau8RE7UwABBAjKQ2FoAOEGHAX7yALMeVaZ4SyrXoACCASK5JgBa8AVL40g8ux30j1XEgABBA5FZ1oFxNdDQxEFmkYAMAAUSWA6FpCF/Rgw6eAPX8JMcugAAiu7EALSaIibL30GRBFgAIIEpbM5+JUENWyMEAQABR6kB8jQAYkIAWPWQBgAAi24FAS0GO4yJSuRye+hovAAggshxIRGGMDthJVA8HAAFEsgOJqGdxAbKiGiCAyAlBGQbc9ewnBvz1NclRDRBAJDkQ2mIRwSENa4Hjq69BHiPYCkcGAAFEtAOhPsdn+AtQYQwtkPGVj3hb4egAIIBICUGQ43BFD0rTncSmGV4AEEBEORDaK8Pna2yNWXwNXKI6XCAAEEAEHQh0HKEiAmuvjNRWOC4AEEDEhCCoSCGrCUVkhwtXiQAGAAGE14HQqMVXdhHToiHU4cJbpgIEEE4HUtrhhgGoGny5mg8aEFgBQACR3OSnNwAIoEE/eAQQQIPegQABNOgdCBBAg96BAAE06B0IEECD3oEAATToHQgQYADqT7NjQjXjUQAAAABJRU5ErkJggg==",
      'Y'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACF0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWHBJMDIySgApaTx6HwIz2BtiLAGaJQ+kRHBI/wTi60Cz/mKTBAggnCEI1PACqhkXkABazEyE49jxOA4EnuJyHAgABBChKH6ERw5ksSgB/SAgh0fuE9Bx7/FpBgggvA4Eav4EpPAZgDcUgXJ8QIoPj358AQAGAAFETCZ5CsS4ogDkOAk8esXxmQsMAHxJCAwAAoigA6GGvMCjRAKazlAAUAyU7nCFHsjM14TsBgGAACKqmCEiw2DL7fhCFm/GQAYAAURKOfgUj5wgNL2BATT0MEIVCghmDGQAEEBEOxBq6Cc8SsShjgOlSxk86ghmDGQAEECk1iT4DOeDhhyo6MGVs4nKGMgAIIBIciDUcHxRLcGAO+0RnTGQAUAAkVMXv2bAnWFA6Q5f6BGVMZABQACR7ECoJfhCERt4T0rGQAYAAURWa4aIDIMMyPEQHAAEECXNLWItfUFqxkAGAAFEtgOBln5jwF/DgMBPaCFPNgAIIEobrCDL8SV8sqMWBgACiCIHQjPMVzxKSM616AAggAZ9kx8ggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCAGAf7+CBAAA36EAQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECKBB70CAABr0DgQIMADOJ4eBx1VJ2wAAAABJRU5ErkJggg==",
      'Z'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABwUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjICKIkgFiayvY/BeIX+DIqQAAN+hAECKBB70CAABr0DgQIoEHvQIAAIiqTQMF7IP5GgnpmIFYioOYnIUMAAogUB/4kxkAkoElAHpSD3xMyBCCAaBXF8kDMhUce5LAXxBgEEEC0cKAIFOMCoGTykFjDAAKI2g4EhZo8Hvm/DBDH/SXWQIAAoqYDQZlCjYAakONIyWgMAAFELQfCHMeMRw1RmQIdAAQQtRwow0ClTIEOAAKIGg4ENSKolinQAUAAUepAPgb8LRySMwU6AAggShzIzkC4piA5U6ADgAAi14GwaozqmQIdAAQQuQ4klCneMJCZKdABQACR40BiMsUT8pyDCQACiFQHEpMp7jFQkCnQAUAAkeJAYjIFyHGktHgIAoAAIra5RUymeAOl+Uh0w1cGPCEOEECMxAx9AHt1IEtVSbSYWHAb6IZPuCQBAmjQN/kBAmjQOxAggAa9AwECaNA7ECCAiMokAwkAAmjQhyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAgAEAvVw8V2FOJ2EAAAAASUVORK5CYII=",
      '1'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAABYUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWKhlECMjoyaQ4sIidRuYET+Ray5AAFElBIGOY2fA7jiKAUAAUexAqOOUqOAWrAAggEiKYqBjmIEUN5QLCjGQ40So7ShkABBApKZBkONUaeEQXAAggAZ9LgYIoEHvQIAAGvQOBAggUtPgVyC+jUVckIFGmQUggEhyILDA/QukMApdYO6mSRkIAgABNOijGCCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECaNA7ECCABr0DAQJo0DsQIIAGvQMBAmjQOxAggAa9AwECiJGUoQ9oo0AaixQ7FGMD34D4D7og0F5srSIMABBApDa3QOr5SNRDUUsHIIAGfRQDBNCgdyBAAA16BwIEEEmZZCAAQAAN+hAECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECDAA6pscAcLpsA0AAAAASUVORK5CYII=",
      '2'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACMElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIUSzYyMjMxAihuI2bCY9Q2IfwLT+E9K7AAIIJIcCHWQINRRvEDMToQekAPfAPFroGP/kupAgABiJCUXAy0zAFLMpFoCBSDH3QPa94kUTQABRGoaJNdxML2qQE/ykaIJIIAGIpMoQZMKUQAggKjlwE9Q/I0ItSDHiRJrMEAAUZKLQQn/EzBNvUcWhIYOyAHSePSCMtgLYiwBCCByHAhy0FNcxQc0p74AOhTExeVIotMhQACR6sDrQAcQE40gAPIIvlAkCgAEEElpkATHMVBaQMMAQADRLBcTyKlEexQggGhZzAjikSO6sAYIIFo6UAKP3Hs8cigAIIBo4kBg9IIch6ue/kRKWgYIIKo7EFqV4cq9oCLoESnmAQQQVR0IdBwXkFLCo+QJqbkbIICo5kCo49QYcDcoXgAd94ZUcwECiKIGKwwAHQfKsfIMuB33Bui4p+SYDRBAFDsQ6DgRBojjcIEX5DoOBAACiNImP8hhIniUPCQnWpEBQACR5UBoLQFKb1w4lJDVesYGAAKIZAdCixFQTsWV3kBl3D1q1cUAAURqnwRUAONroYAcR0p6+0qoIwUQQEQ7kIj0Rg64TSgZAAQQKeUgG4WOIQsABNCgH1kACKBB70CAABr0DgQIIJJy8UAAgAAa9CEIEECD3oEAATToHQgQQIPegQABNOgdCBBAg96BAAE06B0IEGAAGieAZtRikQ0AAAAASUVORK5CYII=",
      '3'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACdUlEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIUSzYyMjFxQM7iwSH8D4p/ANP6TEjsAAogkB0IdJAh1EB+RekAO/AzEL8hxLEAAMRKbi4EWKUEdRwkAOfIpKRoAAoiUNMhMomOwAQmoR4kGAAE0EJlEEOhIEWIVAwQQNRz4F4g/IeG/ROghOqkABBC5uRjkEFDCf48t4UMzkzQD7oxEVAYDAYAAItWB7xkgCf0bPkUgeaAj7wGZmkDMTqIdKAAggIh2INDS26QYDFT/F1rEYHMg0cUNQADROpPgCr3PxBoAEEA0cyAw9OQZcDvwBbHmAAQQpVUdyAHoOZIXiLkZcJebD0mpUQACiCIHMkBCSJpItaDiB+S496RYABBAlDqQWEByFQcDAAFELweCqjhQUngDxK9BOZxYjQABRM+qDpYc1ICOJbpeBwggolszWDVDLOJGEwaJwZpluHLxN6C914mxAyCAKHIgXoMhjldjwN6YBQFQhnlDyByAAKJZFEPTGb7yjqj6GCCAaJ0G8WUGotIhQADR2oFslBoAEEBEFTPA9CQBpECNzBfEpBuoHlAIieJRgrdFBAMAAURKOQjKkfJQx4Jqg89Ax37C4ThQDgapw5VBiHYgQACRU1CzQy0HFb4wi/4gmYfPUTDwk9gqDyCAqFGTEOMgdEB0tQcQQAPRaSKpwQAQQPSqi0EA1MR6hCvd4gIAAURKxx02msDLQEKnhwGSRl8Tm/vRAUAAkV3VERiXAWWaX0D8lZSWCzYAEEA0q4upBQACaNAPvwEE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMB+dSuLcoIYAAAAASUVORK5CYII=",
      '4'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAB0ElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWAgpYGRkJNVMeSAWwSN/FplDKJMCBBC1Q1CEAb/jSAYAAURNB3IBsQwVzQMDgACilgOZgVgJSlMVAAQQtRwIchw7lcxCAQABRA0HSgMxHxXMwQoAAohSBwoCsQSa2F8g/kmhuXAAEECUOBCUKeSxiD9koKIDAQKIXAeCMoM8A2ameArE7ylyERoACCByHQhyHBeaGMhhLyhzDiYACCByHAhKc4JoYt8YIFFLdQAQQKQ6EOQwaTQxUKZ4CKWpDgACiBQHgso5bJniCQMkBGkCAAKIWAfiqilAae4NVV2EBgACiFgHgupY9EzxiQGSa2kKAAKIYHOLAXsLBZTeQCGHqwbBZy6ynj8MBJIHQAAxEmqPAduDoFyLnjGoBT4B7b+NTwFAAA36FjVAAA16BwIE0KB3IEAAEZNJQAn5E4nmcjPgbrwim0Ww/AQIIGIyCfHOQgBVBtw5nKROE0AADfooBgigQe9AgAAa9A4ECKBB70CAABr0DgQIIIK5eKABQAAN+hAECKBB70CAABr0DgQIoEHvQIAAGvQOBAigQe9AgAAa9A4ECDAAWSE1MW+5+MMAAAAASUVORK5CYII=",
      '5'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACGklEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWEhRzMjIaEwti4GZ8ywx6gACaNCHIEAADXoHAgTQoHcgQAANegcCBBBJmQQP+AbET6lkFgoACCBqOfAPMFd+opJZKAAggAZ9FAME0KB3IEAAUSuK2YGFuAQWcVDa/AqM/r/kGgwQQFRzIBBL45IEOh6UPt8DHfqGVIMBAohaDiQE+EAY6FBRIH0P6NCfxGoECCB6p0EuINYEOpSZWA0AATQQmQTkOCViFQMEELlRDEr0XxkgmQA5A4DSIhcU4wOg6OYCRvU3QhYBBBCpDrzNQESuBFoOyzSCeJSB0iVBBwIEEElRDKotiCkyQJkAiO8BmfhqF15i7AQIIFqnQZKLFXQAEEC0diDZBTQMAAQQrR3IhkfuFzEGAAQQ0Q4E9UeAWB6aAYhRDypOsFV/MPCVGHMAAojUXCwCwkDL3wNpEMaaaYDyIlDH4fPMe2IsBAggcstBQSgGOQZUbcGqLnYCjoKBF8Q2IAACiBp1MbGOggFQ2feCWMUAAUTvqg4U0g9JaX4BBBC9WjMgAEpzJDkOBAACiJHY4Tdo7gWlO1A9C6qmiGmR/IU67D25fRaAACLagRgagZU9kALFALaGATjjENMYIAQAAohsB9ILAATQoO80AQTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IECAAQAQtXZP4UnF/gAAAABJRU5ErkJggg==",
      '6'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACp0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIXaBjIyMvIBKWYgZkeT+gPEv4D4JzDd/yTWPIAAoooDgY4SAVIghwkSqeUssWYDBBBFDoQ6TIYBEmI0AQABRJYDgQ4DOUiegfgQIxsABBDJDoQ6Tg2IuajvHEwAEEDkhCAoSgk57hsUI2cGXiDmZiAxOQAEEEkOBIYeKEpF8Ch5D8RPceTSF1Az+EixEyCASA1BaTxyb4AOe0jIAKCaT6RYCBBARBfU0NBDL9tg4BMxjiMHAAQQKTUJvqh5SqlDcAGAACIlinEVKaCa4RswhNmhakCZAdkzoPT4mQESyu9JdSBAADES09yCWq6DQxqUpkBVGL7MAwOgnP0Q5CFiHQgQQMRGMa60BwKg0CLGcSAAKp7UgB4mugwFCCBiHUjNQhlUDioRqxgggKjZ3AJF2ycoJgTYgaEoQYyhAAFEjdbMGyB+AkxXf5EFiWhIgDLUC0KGAwQQNULwJ7rjQAAoBnY4Hn1EJRuAAKJpixrqSAzHwwAx1R5AABHrwD/EOgoL+EqBXgaAACLWgb/wyPFS4gBCACCAiHIggQqem4B2fGUowdAFCCBS0iCuaooZ2pDAANAaCJcDsWYudAAQQKQ4EF8oSkNb2ugAX4FMVL0MEEBE1cVwxYyMoPoYZ4gwQMpEUIHNBsSiDPiLkivEdD8BAohUB4KKBVWiNeAGoFY3wUIaBAACiKRyEJpZKG2YviHWcSAAEEAkV3WgwhcYkiAmOf1hokMOBgACiKQoRtEIyaGg+haU1vA5FJRTQRniBSlDHjAAEEBkOxDFEEj7Dr1IAY/FkNpJQgcAAUQVB9ISAATQoB9+AwigQe9AgAAa9A4ECKBB70CAABr0DgQIoEHvQIAAAwCOWqCNEMWwjwAAAABJRU5ErkJggg==",
      '7'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAB1ElEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECaNA7ECCAWIhRxMjIyAekVKls9ydgBr1NSBFAAA36EAQIoEHvQIAAGvQOBAigQe9AgAAiKpMAwVcgJpigsQA5IGbHIfeXGAMAAogoBwJzG8iwT9jkgDkclzZQzsflOBB4QYzdAAFEyyiWwyP3Boi/EWMIQADRyoESDPijlqjQAwGAAKKFA5kZIA7EBUCO+0msYQABRAsHghzHjEMOFHqvSTEMIICo7UBQtOILvScMROZeGAAIIGo7EF/GAEXrG1INBAggajqQD4pxgUfkGAoQQNR0IL7Q+8SAoxwlBAACiFoOxFesgMBTcg0GCCBqOJBQsUJ0oYwNAAQQNRxIqFghulDGBgACiCIHAuthQsUKSYUyNgAQQJSGIL6MQXKhjA0ABBDZDoT2U/AVKyQXytgAQABREoJUL5SxAYAAIsuBwNAjVKyQVShjAwABRLIDgY4jVKyQXShjAwABRE4I4itWQIDsQhkbAAggkhxIRLFCUaGMDQAEEKkhSKhYoahQxgYAAohxsI8PAgTQoO8XAwTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgQYACZYOE3RqtQDAAAAAElFTkSuQmCC",
      '8'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAAC1klEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIVSAxgZGdmBFAhzoUn9AeJfQPwVmM7/kms+QACR5UCoo0SAWBDqOELqvwGp10CHviHVLoAAYiQ1FwMtkwBS0qRaBAU/gfgR0M5PxGoACCCSHAh0nDwDJOQoBQ+JDU2AACI6k0BDjhqOAwEZoHnMxCgECCCi0iDUMHzRCsoEr4H4M5TPxgBJn3w41IPMEwXiF4TsBgggYjOJIB45ULq6jiWnviGQJHgZiHAgQAARG8X4PPIGTzHyhEjzcQKAAKJGQY2zmCFQ/v0ixnCAACLWgd/wyIkAoxJrNALF0QtvZPCeGIsBAogoB0LLLXyhIQ90jCoWh4richyxZSFAABFdDpJQQIM8AgqdnzjUgxx2j9jqDyCA6FlQgxz0AmgfwZyLDAACiKRMAjT8IZB6yoA/unGB11BMEgAIIJLrYrAm8utjkkMRIIBIjWJBqMMItmAIgDfQ2CAIAAKI6OYW0HEgh0lgkQLXJAwQRwsyENcEAxVNn4COJFjUAAQQUSEIDTklHNKgau4bmnpYWxFXXQwCP4H6rhCyGyCAiM0kuNLbe3THgQCoKQXEtxkgGQoXYAd6BJ8HwAAggAg6EKlJjw3gq2EYoJkBXzTiq2nAACCAiAlBfOmJmDYdXk8QAgABRGljAV8zDAYoyvEAAUTQgQTqTFA6ksfVOoamMXw1D8H6GCCAiC1mQOkIV2iBHMALdAyojwGLTlCLmpuQ47BlMHQAEEDEOhCUG0GhgSvNgaKR1JoFXw6HA4AAIra5BSqMKW4dI4GHxIQeCAAEEKlVHSgU5RjIT/ggRxHtOBAACCByGwugtAVyLDG5GARAafgTOSMLAAFElgNRDICEKihToKdn8NgMKaMI2ABAAFHsQFoDgAAa9MNvAAE06B0IEECD3oEAATToHQgQQIPegQABNOgdCBBgACCC0iLspkTgAAAAAElFTkSuQmCC",
      '9'=>"iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAJ4oUVJvIwAAACBjSFJNAACJawAAjgMAAPRlAACFsgAAcoYAAOnPAAA7dwAAGh+H90zgAAACs0lEQVR4nGL8//8/w2AGAAHENNAOIAQAAmjQOxAggAa9AwECiIVSAxgZGdmBFBcQs6NJfQPir8A0/pcS8wECiCwHAh3FDKREgVgEi8PQ1b4HUm+ADv1Ejl0AAcRIai4GWggKLSVCDsMC3gDxE1JDFCCASHIg0HGgEJMn0WHIABTtt0hxJEAAEZ1JoCFHieNAAGSGBCkaAAKIlDSIz3GgEHnBAAkhUPoEhTQfDrUSQM9+JjZNAgQQUQ4EGijIAPE9LseBou0bkth7oB55qEOxAZA4UQ4ECCBioxhXaIDACzTHwcATBojjsQFBaElAEAAEELEOxBV6IPAemyA0I2CVgwJ8noYDgACi2IFAh/zEow+fHFHFFEAA0bqqwxb1MECUAwECiGIHQosfcgAbMYoAAohYB+ILCVEizSALAAQQsQ7EVySIAENRAkeuJDd04QAggIgtqEG5EV8NIA3CQEeCQvoPVIyoXEoIAAQQUQ4ElXNAy0E1BaFqipQQ+0WMIoAAIiWTwKoyagF8RRAcAAQQ0Q6EFry3GPAXvsjgEwG1RDkQIIBIarBCHXkPWrSA6mdQOkOO1m9Q/B7UGIDWx7gAUbEBEEBktaihdS8IP8WlBpqrBXFIfyNQA8EBQADRsiYBlY+4GgTEJhMGgACiiQOhLW9pHNKgZPKaWLMAAoikKIamvZ+4muzQHh7IcfiKoxekNPkBAojUNAgKFT6gQ0A5FJQGYRaBohLkeEKFM0gP0aEHAgABRG6/mI8Ix6ADcAlAaq8OIIAo7rgTCUAhd4/YnIsMAAKI1g4Ed6aADntBrgEAAUQrB4ILayB+TenQB0AAkdpxB+VSEGZmwGwRg1oxoAYAxeMxyAAggEge+qA3AAigQT/8BhBAg96BAAE06B0IEECD3oEAATToHQgQQIPegQABBgA706r6piv1AwAAAABJRU5ErkJggg==");
  }
}

$captchaSupport=new CaptchaSupport;


?>
