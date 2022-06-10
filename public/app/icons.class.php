<?php
	/**
	 * icons.class.php
	 *
	 * Functions to return different icons
	 *
	 * @package    Storio
	 * @author     Alex White
	 * @copyright  2022 Storio
	 * @link       https://github.com/ialexpw/Storio
	 */

	/**
	 * Icon Class
	 */
	class StoIco {
		public static function ShowIcon($ico): string
		{
			// Grab the extension
			$ext = strtolower(pathinfo($ico, PATHINFO_EXTENSION));

			////
			// Document icons
			////
			$docArr = array(
				"doc",
				"docx",
				"rtf"
			);

			// In arr
			if(in_array($ext, $docArr)) {
				return 'far fa-file-word';
			}

			////
			// Video icons
			////
			$vidArr = array(
				"webm",
				"mp4"
			);

			// In arr
			if(in_array($ext, $vidArr)) {
				return 'far fa-file-video';
			}

			////
			// Image icons
			////
			$imgArr = array(
				"png",
				"jpeg",
				"jpg",
				"gif",
				"apng",
				"ico",
				"svg",
				"tiff",
				"webp"
			);

			// In arr
			if(in_array($ext, $imgArr)) {
				return 'far fa-file-image';
			}

			////
			// Archive icons
			////
			$arcArr = array(
				"zip",
				"rar",
				"7z"
			);

			// In arr
			if(in_array($ext, $arcArr)) {
				return 'far fa-file-archive';
			}

			////
			// Text/source icons
			////
			$txtArr = array(
				"asm",
				"atom",
				"c",
				"cpp",
				"cs",
				"css",
				"d",
				"dart",
				"docker",
				"dockerfile",
				"go",
				"h",
				"htm",
				"html",
				"ini",
				"js",
				"javascript",
				"json",
				"less",
				"lua",
				"makefile",
				"markdown",
				"md",
				"nginx",
				"perl",
				"php",
				"py",
				"python",
				"rb",
				"rss",
				"ruby",
				"rust",
				"sass",
				"scss",
				"sh",
				"smarty",
				"sql",
				"twig",
				"txt",
				"vbnet",
				"vim",
				"xml",
				"yml",
				"yaml"
			);

			// In arr
			if(in_array($ext, $txtArr)) {
				return 'far fa-file-code';
			}

			////
			// Audio icons
			////
			$audArr = array(
				"mp3",
				"ogg"
			);

			// In arr
			if(in_array($ext, $audArr)) {
				return 'far fa-file-audio';
			}

			////
			// Unique icons
			////

			// PDF
			if($ext == 'pdf') {
				return 'far fa-file-pdf';
			}

			// Excel
			if($ext == 'xls' || $ext == 'xlsx') {
				return 'far fa-file-excel';
			}

			// Powerpoint
			if($ext == 'pps' || $ext == 'ppt') {
				return 'far fa-file-powerpoint';
			}

			// Default icon
			return 'far fa-file';
		}
	}
?>