<?php
	/*
	- Copyright 2013 Vyrazu Labs
	- Subject to the terms and conditions of this License, each Contributor hereby grants to 
	- You a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright 
	- license to reproduce, prepare Derivative Works of, publicly display, publicly perform, 
	- sublicense, and distribute the Work and such Derivative Works in Source or Object form.
	
	- We will be happy to get the attribution of the work we have provided.
	
	- Requires GD Module
	- Requires ffmpeg Module
	- Requires Imagick Module
	*/
	class libraryMedia
	{
		/*
		- variable to store the path the ffmpeg
		*/
		private $ffmpeg;
		
		function __construct()
		{
			//absolute path for the ffmpeg module
			$this->ffmpeg = "/usr/local/bin/ffmpeg";
		}
		
		/*
		- method of ffmpeg to take the snapshot
		- Auth Singh
		*/
		function getSnaps($moviePath,$no_snapshots,$video_h,$video_w,$output_path,$output_fileName)
		{
			//get the duration of the movie
			$movie = new ffmpeg_movie($moviePath,0);
			$movieDuration = $movie->getDuration();
			//duration in second
			//echo $movieDuration = ($movieDuration/60);
			//get frame rate for the snapshots
			$frameRate = ($no_snapshots)/$movieDuration ;
			//$frameRate = round($frameRate,2);
			$resolution = $video_h."x".$video_w;
			exec("$this->ffmpeg -ss 00:00:20 -i $moviePath -r $frameRate -s $resolution -f image2 $output_path".$output_fileName."%05d.jpg " );
		}
		
		/*
		- method to convert the video to a particular format
		- @param $resolution example "320x240"
		- Auth Singh
		*/
		function convertVideo($inputFile,$outPath,$outputFormat,$outputFilename,$resolution)
		{
			exec("$this->ffmpeg -i $inputFile -c:v libx264 -c:a copy -s $resolution -f $outputFormat $outPath".$outputFilename );
			return "1";
		}
		
		/*
		- method to slice the video
		- @param time format tt:hh:ss
		- @param resoltution format example 1200x768
		- Auth Singh
		*/
		function sliceVideo($inputFile,$startTime,$interval,$outPath,$outputFormat,$outputFilename,$resolution)
		{
			exec("$this->ffmpeg -ss $startTime -i $inputFile -c:v libx264 -c:a copy -s $resolution -async 1 -t $interval $outPath".$outputFilename);
		}
		
		/*
		- method to resize the image
		- @param absolute path of both input and output files
		- Auth Singh
		*/
		function resizeImage($inputFile,$imageWidth,$imageHeight,$outputFile)
		{
			$tempImage = new Imagick($inputFile);
			$tempImage->resizeImage($imageWidth,$imageHeight,Imagick::FILTER_LANCZOS,1);
			$tempImage->writeImage($outputFile);
		
			$tempImage->destroy(); 
		}
		
		/*
		- method to take the image and retutn height/width for aspect 
		- ratio calculation
		- @param absolute path of both input
		- Auth Singh
		*/
		function getImageAspect($inputFile)
		{
			$tempImage = new Imagick($inputFile) ;
			$imageGeometry = $tempImage->getImageGeometry() ;
			return $imageGeometry['height']/$imageGeometry['width'] ;
		}
		
		/*
		- method to take the video and return video duration
		- @param absolute path of input video
		- Auth Singh
		*/
		function getVideoLength($inputVideo)
		{
			$movie = new ffmpeg_movie($inputVideo,0) ;
			$movieDuration = $movie->getDuration() ;
			return $movieDuration ;
		}
		
		/*
		- generate thumbnail from the video
		- using ffmpeg 
		- time after a tumbnail is captured is 25 sec
		- @param resolution format example 1200x900 
		- Auth Singh
		*/
		function getThumbs($moviePath,$startTime,$resolution,$output_path,$output_fileName,$outputFormat)
		{
			exec("$this->ffmpeg -ss $startTime -i $moviePath -s $resolution -frames:v 1 $output_path".$output_fileName.".".$outputFormat);
		}
		
		/*
		- generate thumb for vids by merging two images
		- the output image will contain a play sign
		- Auth Singh
		*/
		function mergeImage($image1Path,$image1OutputPath,$image1Filename,$Format)
		{
			//path for the play sign
			$path_play_sym = "" ;//put the path of the file such as a play symbol 
			$thumb = new Imagick($image1Path);
			$play_sym = new Imagick($path_play_sym);
			
			$thumb->compositeImage($play_sym , Imagick::COMPOSITE_DEFAULT, 0, 0 );
			
			$thumb->writeImage($image1OutputPath.$image1Filename.".".$Format);
		}
	}

?>