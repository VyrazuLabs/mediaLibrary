<?php
	/*
	* Copyright 2013 Vyrazu Labs
	* Subject to the terms and conditions of this License, each Contributor hereby grants to 
	* You a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright 
	* license to reproduce, prepare Derivative Works of, publicly display, publicly perform, 
	* sublicense, and distribute the Work and such Derivative Works in Source or Object form.
	
	* We will be happy to get the attribution of the work we have provided.
	
	* Requires GD Module
	* Requires ffmpeg Module
	* Requires Imagick Module
	*/
	class libraryMedia
	{
		/*
		* variable to store the path the ffmpeg
		*/
		public $ffmpeg;
		
		/*
		* method of ffmpeg to take the snapshot
		* @var string
		* @author Singh
		*/
		public function getSnaps($moviePath,$no_snapshots,$video_h,$video_w,$output_path,$output_fileName)
		{
			//get the duration of the movie
			$movie = new ffmpeg_movie($moviePath,0);
			$movieDuration = $movie->getDuration();
			//duration in second

			//get frame rate for the snapshots
			$frameRate = ($no_snapshots)/$movieDuration ;
			//$frameRate = round($frameRate,2);
			$resolution = $video_h."x".$video_w;
			try{
				exec("$this->ffmpeg -ss 00:00:20 -i $moviePath -r $frameRate -s $resolution -f image2 $output_path".$output_fileName."%05d.jpg " );
				return 1;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
		}
		
		/*
		- method to convert the video to a particular format
		- @author Singh
		*/
		public function convertVideo($inputFile,$outPath,$outputFormat,$outputFilename,$resolution)
		{
			try{
				exec("$this->ffmpeg -i $inputFile -c:v libx264 -c:a copy -s $resolution -f $outputFormat $outPath".$outputFilename );
				return 1;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* method to slice the video
		* @author Singh
		*/
		public function sliceVideo($inputFile,$startTime,$interval,$outPath,$outputFormat,$outputFilename,$resolution)
		{
			try{
				exec("$this->ffmpeg -ss $startTime -i $inputFile -c:v libx264 -c:a copy -s $resolution -async 1 -t $interval $outPath".$outputFilename);
				return 1;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* method to resize the image
		* @author Singh
		*/
		public function resizeImage($inputFile,$imageWidth,$imageHeight,$outputFile)
		{
			try {
				$tempImage = new Imagick($inputFile);
				$tempImage->resizeImage($imageWidth,$imageHeight,Imagick::FILTER_LANCZOS,1);
				$tempImage->writeImage($outputFile);
				$tempImage->destroy();
				return 1;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* method to take the image and retutn height/width for aspect 
		* ratio calculation
		* @author Singh
		*/
		public function getImageAspect($inputFile)
		{
			try {
				$tempImage = new Imagick($inputFile) ;
				$imageGeometry = $tempImage->getImageGeometry() ;
				return $imageGeometry['height']/$imageGeometry['width'] ;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* method to take the video and return video duration
		* @author Singh
		*/
		public function getVideoLength($inputVideo)
		{
			try {
				$movie = new ffmpeg_movie($inputVideo,0) ;
				$movieDuration = $movie->getDuration() ;
				return $movieDuration ;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* generate thumbnail from the video
		* using ffmpeg 
		* time after a tumbnail is captured is 25 sec
		* @author Singh
		*/
		public function getThumbs($moviePath,$startTime,$resolution,$output_path,$output_fileName,$outputFormat)
		{
			try {
				exec("$this->ffmpeg -ss $startTime -i $moviePath -s $resolution -frames:v 1 $output_path".$output_fileName.".".$outputFormat);
				return 1 ;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
		
		/*
		* generate thumb for vids by merging two images
		* the output image will contain a play sign
		* @author Singh
		*/
		public function mergeImage($image1Path,$image1OutputPath,$image1Filename,$Format)
		{
			try {
				//path for the play sign
				$path_play_sym = "" ;//put the path of the file such as a play symbol 
				$thumb = new Imagick($image1Path);
				$play_sym = new Imagick($path_play_sym);
				
				$thumb->compositeImage($play_sym , Imagick::COMPOSITE_DEFAULT, 0, 0 );
				
				$thumb->writeImage($image1OutputPath.$image1Filename.".".$Format);
				return 1 ;
			}
			catch (Exception $e) {
			    echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	}

?>