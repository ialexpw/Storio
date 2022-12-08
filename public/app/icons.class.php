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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-word img-icon" viewBox="0 0 16 16">
				<path d="M5.485 6.879a.5.5 0 1 0-.97.242l1.5 6a.5.5 0 0 0 .967.01L8 9.402l1.018 3.73a.5.5 0 0 0 .967-.01l1.5-6a.5.5 0 0 0-.97-.242l-1.036 4.144-.997-3.655a.5.5 0 0 0-.964 0l-.997 3.655L5.485 6.88z"/>
				<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
			  </svg>';
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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-play img-icon" viewBox="0 0 16 16">
				<path d="M6 6.883v4.234a.5.5 0 0 0 .757.429l3.528-2.117a.5.5 0 0 0 0-.858L6.757 6.454a.5.5 0 0 0-.757.43z"/>
				<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
			  </svg>';
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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-image img-icon" viewBox="0 0 16 16">
				<path d="M6.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
				<path d="M14 14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5V14zM4 1a1 1 0 0 0-1 1v10l2.224-2.224a.5.5 0 0 1 .61-.075L8 11l2.157-3.02a.5.5 0 0 1 .76-.063L13 10V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4z"/>
			  </svg>';
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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-zip img-icon" viewBox="0 0 16 16">
				<path d="M5 7.5a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v.938l.4 1.599a1 1 0 0 1-.416 1.074l-.93.62a1 1 0 0 1-1.11 0l-.929-.62a1 1 0 0 1-.415-1.074L5 8.438V7.5zm2 0H6v.938a1 1 0 0 1-.03.243l-.4 1.598.93.62.929-.62-.4-1.598A1 1 0 0 1 7 8.438V7.5z"/>
				<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1h-2v1h-1v1h1v1h-1v1h1v1H6V5H5V4h1V3H5V2h1V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
			  </svg>';
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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-code img-icon" viewBox="0 0 16 16">
				<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
				<path d="M8.646 6.646a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L10.293 9 8.646 7.354a.5.5 0 0 1 0-.708zm-1.292 0a.5.5 0 0 0-.708 0l-2 2a.5.5 0 0 0 0 .708l2 2a.5.5 0 0 0 .708-.708L5.707 9l1.647-1.646a.5.5 0 0 0 0-.708z"/>
			  </svg>';
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
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-music img-icon" viewBox="0 0 16 16">
				<path d="M11 6.64a1 1 0 0 0-1.243-.97l-1 .25A1 1 0 0 0 8 6.89v4.306A2.572 2.572 0 0 0 7 11c-.5 0-.974.134-1.338.377-.36.24-.662.628-.662 1.123s.301.883.662 1.123c.364.243.839.377 1.338.377.5 0 .974-.134 1.338-.377.36-.24.662-.628.662-1.123V8.89l2-.5V6.64z"/>
				<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
			  </svg>';
			}

			////
			// Unique icons
			////

			// PDF
			if($ext == 'pdf') {
				//return 'far fa-file-pdf';
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-pdf img-icon" viewBox="0 0 16 16">
				<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
				<path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
			  </svg>';
			}

			// Excel
			if($ext == 'xls' || $ext == 'xlsx') {
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-excel img-icon" viewBox="0 0 16 16">
				<path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
				<path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
			  </svg>';
			}

			// Powerpoint
			if($ext == 'pps' || $ext == 'ppt' || $ext == 'pptx') {
				return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark-ppt img-icon" viewBox="0 0 16 16">
				<path d="M7 5.5a1 1 0 0 0-1 1V13a.5.5 0 0 0 1 0v-2h1.188a2.75 2.75 0 0 0 0-5.5H7zM8.188 10H7V6.5h1.188a1.75 1.75 0 1 1 0 3.5z"/>
				<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
			  </svg>';
			}

			// Default icon
			return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-file-earmark img-icon" viewBox="0 0 16 16">
			<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
		  </svg>';
		}
	}
?>