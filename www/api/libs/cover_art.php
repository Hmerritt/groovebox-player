<?php




/*

  https://github.com/Hmerritt/groovebox-player

  This file contains functions on extracting the cover-art from a file on the server
  Example usage;

  /radio/api/?coverArt&mount=disco

*/








//  import the stream class
require_once("libs/stream.php");








//  metadata class to retrieve data from icecast endpoints such as; status-json.xsl
class CoverArt
{
    private $settings = array();

    public function __construct(array $settings)
    {

        //  import the user's settings
        $this->settings = $settings;


    }

    //  get the cover-art for the currently playing track on a specific playlist
    public function currentlyPlaying($playlist)
    {


        //  get the currently playing song by loaded latest stream data
        $currentTrack = (new Stream($this->settings))->metadata($playlist)["track"];


        //  create path from the track name
        $filePath = "../tracks/" . $playlist . "/" . $currentTrack;



        //  start getid3 instance
        $getID3 = new getID3;

        //  open file and extract metadata
        $fileInfo = $getID3->analyze($filePath);


        

        //  set a default cover as a fallback
        $defaultCover = array(1,2,3,4,5,6,7);
        $rand_key = array_rand($defaultCover, 1);
        $defaultCover = '../client/img/default-cover'. $defaultCover[$rand_key] .'.png';



        //  check if track has an album cover
        if (isset($fileInfo["comments"]["picture"][0]["data"]) ||
            isset($fileInfo["id3v2"]["APIC"][0]["data"]))
        {


            //  check which array index exists
            if (isset($fileInfo["id3v2"]["APIC"][0]["data"]))
            {

                //  use this index from now on
                $audioImage = $fileInfo["id3v2"]["APIC"][0];

            } else
            {

                //  use this index from now on
                $audioImage = $fileInfo["comments"]["picture"][0];

            }



            //  check for the image type (jpeg/png)
            if (isset($audioImage["image_mime"]))
            {

                //  if a type exists - use it when creating the content header
                $mimetype = $audioImage["image_mime"];

            } else
            {

                //  if not type if found - use jpeg as a fallback
                $mimetype = "image/jpeg";

            }


            //  set image header - interpret data as an image
            header("Content-Type: $mimetype");



            //  echo the image to the user
            return $audioImage["data"];


        } else
        {

            //  use default cover
            header("Content-Type: image/x-png");
            return readfile($defaultCover);

        }


    }








    //  get the cover-art for any track on a specific mount
    public function track($playlist, $trackName)
    {





    }








}






?>
