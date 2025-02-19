<?php

// v.0.1      [08.08.2007]
//         www.paggard.com

//define('EURO',chr(128));

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);

require_once ('fpdf.php');
require_once ('pdf_engine.php');

//-------------------------------------------------------------------------------------------------
class PDF_DRIVER {

	var $_pass;

	var $temp_dir;
	var $rnd_proc_nm;
	var $level;

	var $default_units;
	var $multilingual_support;
	var $doc_encoding;

	var $p_x;
	var $p_y;


	var $header = "";
	var $text = "";
	var $pdf;


	var $pg_width;
	var $pg_height;
	var $mar_left;
	var $mar_right;
	var $mar_top;
	var $mar_bott;
	var $facing_pages;
	var $gutter_width;
	var $rtl_gutter;

	var $image_size;

	var $header_align;
	var $footer_align;
	var $head_y;
	var $foot_y;
	var $page_numbers;
	var $page_numbers_valign;
	var $page_numbers_align;
	var $pn_autoInsert;
	var $page_orientation;

	var $line_height;


	var $curr_font_settings;
	var $prev_font_settings;
	var $font_face;
	var $font_size;
	var $def_par_before;
	var $def_par_after;
	var $def_par_align;
	var $def_par_lines;
	var $def_par_lindent;
	var $def_par_rindent;
	var $def_par_findent;
	var $tbl_def_border;
	var $tbl_def_width;
	var $tbl_def_align;
	var $tbl_def_valign;
	var $tbl_def_bgcolor;
	var $row_def_align;
	var $img_def_border;
	var $img_def_src;
	var $img_def_width;
	var $img_def_height;
	var $img_def_left;
	var $img_def_top;
	var $img_def_space;
	var $img_def_align;
	var $img_def_wrap;
	var $img_def_anchor;

	var $h_link_fontf;
	var $h_link_fonts;
	var $h_link_fontd;

	var $fnt_type;
	var $fnt_color;
	var $fnt_fontf;
	var $fnt_fonts;
	var $fnt_fontd;

	var $tbl_level;
	var $curr_tbl_settings;
	var $tbl_all_data_head;
	var $tbl_all_data_body;
	var $tbl_all_data_wdth;
	var $tbl_cells_wdth;
	var $tr_hd_mass;
	var $tb_wdth;
	var $table_data;
	var $c_table_id = 0;
	var $c_table_id_a = array();
	var $c_table_td_id_a = array();
	var $table_fin_wdth = array();
	var $c_table_fin_data = array();


	var $inCell = array();
	var $t_before = array();
	var $t_x = array();
	var $t_y = array();
	var $t_table_y = array();
	var $t_table_y_pg = array();
	var $t_y_matrix_pg = array();
	var $t_y_matrix_pg_e = array();
	var $t_y_matrix = array();
	var $t_y_matrix_e = array();
	var $t_last_w = array();
	var $t_clspn_cnt = array();
	var $t_rwspn_cnt = array();
	var $t_c_row = array();
	var $t_c_row_p = array();


	var $inTable;
	var $color_table;
	var $image_array;
	var $image_token;
	var $image_counter;

	var $c_fspt;
	var $hf_align;
	var $_page_tot;
	var $_page_curr;
	var $_in_footnote;



// ------------------------------------------------------------------------------------------------
// CONSTRUCTOR
	function PDF_DRIVER() {
		$this->_pass = 2;
		$this->color_table = array();
		$this->image_array = array();
		$this->image_token = " img12365412img ";
		$this->_page_tot = "{iii}";
		$this->_page_curr = "{lll}";
		$this->image_counter = 0;
		$this->_in_footnote = false;
		$this->curr_font_settings = array();

		$this->curr_tbl_settings = array();
		$this->tbl_all_data_head = array();
		$this->tbl_all_data_body = array();
		$this->tbl_all_data_wdth = array();
		$this->tbl_cells_wdth = array();
		$this->tr_hd_mass = array();
		$this->table_data = array();
		$this->tb_wdth = array();
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iDOCUMENT($attribs,$EndOfTag) {
		if (!$EndOfTag) {
			if (isset($attribs["CONFIG_FILE"]) && $attribs["CONFIG_FILE"]!="") {
				if ($this->file_check($attribs["CONFIG_FILE"])) {
					include $attribs["CONFIG_FILE"];
				}
			}
			else {die("Configuration file was not provided - script terminated.");}
			$this->level = 1;
			$this->temp_dir = $temp_dir;
			$this->rnd_proc_nm = time();

			$this->default_units = $default_units;
			$this->multilingual_support = $multilingual_support;
			//-----
			if ($this->multilingual_support) {
				if (!function_exists("mb_convert_encoding")) {
					//trigger_error ("You have turned on the multilingual support, but do not have 'mb_string' PHP extention enabled.<br>\n", E_USER_ERROR);
				}
			}
			//-----

			$this->pg_width=$this->mms($pg_width);
			$this->pg_height=$this->mms($pg_height);
			$this->mar_left=$this->mms($mar_left);
			$this->mar_right=$this->mms($mar_right);
			$this->mar_top=$this->mms($mar_top);
			$this->mar_bott=$this->mms($mar_bott);

			$this->facing_pages=$facing_pages;
			$this->rtl_gutter=$rtl_gutter;
			$this->gutter_width=$this->mms($gutter_width);

			$this->header_align = $this->get_align($header_align);
			$this->footer_align = $this->get_align($footer_align);

			$this->image_size = $image_size;
			$this->line_height = 5; // TODO

			$this->head_y=$this->mms($head_y);
			$this->foot_y=$this->mms($foot_y);
			$this->page_numbers = $page_numbers;
			$this->page_numbers_valign = $page_numbers_valign;
			$this->page_numbers_align = $page_numbers_align;
			$this->pn_autoInsert = $page_numbers_autoinsert;
			$this->page_orientation = $page_orientation;

			$this->font_face=$font_face;
			$this->font_size=$font_size;
			$this->def_par_before=$def_par_before;
			$this->def_par_after=$def_par_after;
			$this->def_par_align=$this->get_align($def_par_align);
			$this->def_par_lines=$def_par_lines;
			$this->def_par_lindent=$def_par_lindent;
			$this->def_par_rindent=$def_par_rindent;
			$this->def_par_findent=$def_par_findent;
			$this->tbl_def_border=$tbl_def_border;
			$this->tbl_def_width=$tbl_def_width;
			$this->tbl_def_cellpadding=$tbl_def_cellpadding;
			$this->tbl_def_align=$tbl_def_align;
			$this->tbl_def_valign=$tbl_def_valign;
			$this->tbl_def_bgcolor=$tbl_def_bgcolor;
			$this->row_def_align=$row_def_align;
			$this->img_def_border=$img_def_border;
			$this->img_def_src=$img_def_src;
			$this->img_def_width=$img_def_width;
			$this->img_def_height=$img_def_height;
			$this->img_def_left=$img_def_left;
			$this->img_def_top=$img_def_top;
			$this->img_def_space=$img_def_space;
			$this->img_def_align=$img_def_align;
			$this->img_def_wrap=$img_def_wrap;
			$this->img_def_anchor=$img_def_anchor;

			$this->h_link_fontf=$h_link_fontf;
			$this->h_link_fonts=$h_link_fonts;
			$this->h_link_fontd=$h_link_fontd;

			$this->fnt_type=@$fnt_type;
			$this->fnt_color=$fnt_color;
			$this->fnt_fontf=$fnt_fontf;
			$this->fnt_fonts=$fnt_fonts;
			$this->fnt_fontd=$fnt_fontd;

			if (strtolower($this->page_orientation) == "landscape") {
				$this->pg_height=$this->mms($pg_width);
				$this->pg_width=$this->mms($pg_height);
				$this->mar_top=$this->mms($mar_left);
				$this->mar_bott=$this->mms($mar_right);
				$this->mar_left=$this->mms($mar_top);
				$this->mar_right=$this->mms($mar_bott);
			}

			$this->tbl_level = 0;
			$this->_first_par = 1;
			$this->inTable = false;

			$hlink = $this->get_pdf_color($h_link_color);
			$ftn_color = $this->get_pdf_color(preg_replace("/\#/","",$fnt_color));

			$this->curr_font_settings[$this->level]["FACE"] = $this->font_face;
			$this->curr_font_settings[$this->level]["SIZE"] = $this->font_size;
			$this->curr_font_settings[$this->level]["COLOR"] = "";
			$this->curr_font_settings[$this->level]["STYLE"] = "";

			if ($this->_pass == 2) {
				// -- starting PDF engine ------------------------
				//$this->pdf=new PDF($this->page_orientation,"mm",array($this->pg_width,$this->pg_height));
				$this->pdf=new PDF("portrait","mm",array($this->pg_width,$this->pg_height));
					$this->pdf->AliasNbPages($this->_page_tot);
					$this->pdf->AliasCurrPage($this->_page_curr);
				$this->pdf->SetMargins($this->mar_left, $this->mar_top, $this->mar_right);
				//--- document properties
				if (isset($attribs["AUTHOR"])) {$this->pdf->SetAuthor($attribs["AUTHOR"]);}
				if (isset($attribs["TITLE"])) {$this->pdf->SetTitle($attribs["TITLE"]);}
				if (isset($attribs["SUBJECT"])) {$this->pdf->SetSubject($attribs["SUBJECT"]);}
				if (isset($attribs["KEYWORDS"])) {$this->pdf->SetKeywords($attribs["KEYWORDS"]);}
				if (isset($attribs["OPERATOR"])) {$this->pdf->SetCreator($attribs["OPERATOR"]);}
				//-----------------------
				$this->pdf->CellAlign = $this->def_par_align;
				$this->pdf->AddPage();
				$this->pdf->SetFont($this->font($this->font_face),'',$this->font_size);
				$this->pdf->c_slines = $this->def_par_lines;
			}
			// -----------------------------------------------

		}// end of IF
		else { // END OF TAG
			if ($this->_pass == 2) {
				$this->pdf->render_line(true);
			}
			$this->c_table_id = 0;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _set_encoding($encoding) {
		$this->doc_encoding = $encoding;
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
	function mms($num) { // great thanks to Ian M. Nordby for this function
		//added units recognition -- assumes 1pt=1/72in exactly (IMN)...
		if (preg_match('/^(-?[0-9]+(\.[0-9]+)?)[ ]?(mm|cm|q|kyu|in|pt|pts|picas|twips)$/i',trim($num),$regs)) {
			$units = strtolower($regs[3]);
			$num = (float)$regs[1];
		}
		else {
			$units = $this->default_units;
		}
		switch ($units) { //unit type
			case 'cm'   : $sum = round($num*567); break; //centimeters (actual ~566.929)
			case 'mm'   : $sum = round($num*56.7); break; //millimeters (=1/10 cm)
			case 'q'    : //alias of 'kyu'
			case 'kyu'  : $sum = round($num*14.175); break; //Q/kyu (=1/4 mm)
			case 'in'   : $sum = round($num*1440); break; //inches
			case 'pt'   : //alias of 'pts' (points)
			case 'pts'  : $sum = round($num*20); break; //pt/pts (=1/72 in)
			case 'picas': $sum = round($num*240); break; //picas (=12 pts or 1/6 in)
			case 'twips': $sum = round($num); break; //twips (=1/20 pt or 1/1440 in)
		}
		return $sum/56.7; // we are returning millimeters
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
	function font($font) {
		$perm = false;
		switch (strtolower($font)) {
			case "sym": $perm = "Symbol"; break;
			case "symbol": $perm = "Symbol"; break;
			case "arial": $perm = "Helvetica"; break;
			case "roman": $perm = "Times"; break;
			case "courier": $perm = "Courier"; break;
			case "seriff": $perm = "Times"; break;
			case "garamond": $perm = ""; break;
			case "verdana": $perm = ""; break;
			case "cur": $perm = "Courier"; break;
			case "helvetica": $perm = "Helvetica"; break;
			case "wingdings": $perm = "ZapfDingbats"; break;
			case "wingdings2": $perm = "ZapfDingbats"; break;
			case "wingdings3": $perm = "ZapfDingbats"; break;
			case "arial_narrow": $perm = ""; break;
			case "futura": $perm = ""; break;
			case "tahoma": $perm = ""; break;
		}
		return $perm;
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function b_style($style) {
		$perms = "";
		switch (strtoupper($style)) {
			case "SHADOWED": $perms .= "brdrsh"; break; //Shadowed border.
			case "DOUBLE": $perms .= "brdrdb"; break; //Double border.
			case "DOTTED": $perms .= "brdrdot"; break; //Dotted border.
			case "DASHED": $perms .= "brdrdash"; break; //Dashed border.
			case "HAIRLINE": $perms .= "brdrhair"; break; //Hairline border.
			case "INSET": $perms .= "brdrinset"; break; //Inset border.
			case "DASH": $perms .= "brdrdashsm"; break; //Dash border (small).
			case "DOT": $perms .= "brdrdashd"; break; //Dot dash border.
			case "DDDASH": $perms .= "brdrdashdd"; break; //Dot dot dash border.
			case "OUTSET": $perms .= "brdroutset"; break; //Outset border.
			case "TRIPLE": $perms .= "brdrtriple"; break; //Triple border.
			//case "Thick": $perms .= "brdrtnthsg"; break; //Thick thin border (small).
			//case "Thin": $perms .= "brdrthtnsg"; break; //Thin thick border (small).
			//case "Thin": $perms .= "brdrtnthtnsg"; break; //Thin thick thin border (small).
			//case "Thick": $perms .= "brdrtnthmg"; break; //Thick thin border (medium).
			//case "Thin": $perms .= "brdrthtnmg"; break; //Thin thick border (medium).
			//case "Thin": $perms .= "brdrtnthtnmg"; break; //Thin thick thin border (medium).
			//case "Thick": $perms .= "brdrtnthlg"; break; //Thick thin border (large).
			//case "Thin": $perms .= "brdrthtnlg"; break; //Thin thick border (large).
			//case "Thin": $perms .= "brdrtnthtnlg"; break; //Thin thick thin border (large).
			case "WAVY": $perms .= "brdrwavy"; break; //Wavy border.
			case "DOUBLEW": $perms .= "brdrwavydb"; break; //Double wavy border.
			case "STRIPED": $perms .= "brdrdashdotstr"; break; //Striped border.
			case "EMBOSS": $perms .= "brdremboss"; break; //Emboss border.
			case "ENGRAVE": $perms .= $slash."brdrengrave"; break; //Engrave border.
		}
		return $perms;
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function get_align($variable) {
		switch (strtolower($variable)) {
			case "center": $variable = "C"; break;
			case "left": $variable = "L"; break;
			case "right": $variable = "R"; break;
			case "justify": $variable = "FJ"; break;
		}
		return $variable;
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _add_code($code) {
		if ($this->_pass < 2) { return; }

		if ($this->_in_footnote) {
			return;
		}
		else if ($this->tbl_level>0) {
			if (isset($this->table_data[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["c_num"]])) {
				$this->table_data[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["c_num"]] .= $code;
			}
			else {
				$this->table_data[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["c_num"]] = $code;
			}
			$this->pdf->myWrite($this->line_height, $code);
		}
		else {
			$this->pdf->myWrite($this->line_height, $code);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------
	function add_text($string) {
		if ($this->_pass < 2) { return; }
		// need to set the correct encoding in the head of XML
		// like this - xml version="1.0" encoding="windows-1251"
		// and after that use the same encoding to decode the scrings
		// plus - use the correct character set in RTF fonts
		// ---
		if ($this->multilingual_support && strtoupper($this->doc_encoding) != "UTF-8") {
			//$string = @mb_convert_encoding($string, $this->doc_encoding, "auto");
		}
		else if ($this->multilingual_support && strtoupper($this->doc_encoding) == "UTF-8") {
			// trying to create an automatic conversion to UNICODE points
			//$string = convert_to_unicode_points($string);
		}

		// ---
		$fig_l     = "pag456pag";
		$fig_r     = "pag654pag";
		$slash     = "pp345pag1223pp";
		$star      = "pp346pag1224pp"; // *
		$quote     = "pp375pag1225pp"; // "
		$string    = preg_replace("/[\r\n\t]+/","",$string);
		$string    = preg_replace("/##amp##/msi","&",$string);
		$string    = preg_replace("/&amp;/msi","&",$string);
		$string    = preg_replace("/&#U8364/msi","&#U128",$string); // TODO
		//$string    = preg_replace("/&#([0-9]+)/e","chr('\\1')",$string);
		//$string    = preg_replace("/&#([0-9]+);/",$slash."u\\1  ",$string);
		$string    = preg_replace("/&#U([0-9]+)/e","chr('\\1')",$string);

		if (function_exists('htmlspecialchars_decode')) {
			/* TESTING */ $string    = htmlspecialchars_decode($string);
		}
		$string    = html_entity_decode($string);
		//$fin       = rawurlencode($string);
		//$fin       = $string;
		$r_srch = array (
								"'%20'",
								"'%92'",
								"'%'",
								"'\\'5C'",
								"'".$slash."'",
								"'".$fig_l."'",
								"'".$fig_r."'",
								"'".$star."'",
								"'".$quote."'"
							);
		$r_rplc = array (
								" ",
								"%27",
								"\'",
								"\\",
								"\\",
								"{",
								"}",
								"*",
								"\""
							);

		//$this->_add_code(preg_replace($r_srch,$r_rplc,$fin));
		$this->_add_code($string);
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iIMG($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$temp_image_data = $this->parse_image($attribs);
			return;
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
	function parse_image($attribs) {
		if (isset($attribs["SRC"])) {
			$img_src = $attribs["SRC"];
		}
		else {$img_src = $this->img_def_src;}
		$img_src = preg_replace("/##amp##/msi","&",$img_src);


		if (isset($attribs["TOP"])) {$img_top = $attribs["TOP"];}
		else {$img_top = $this->img_def_top;}
		if (isset($attribs["WIDTH"])) {$img_width = $attribs["WIDTH"];}
		else {$img_width = $this->img_def_width;}
		if (isset($attribs["HEIGHT"])) {$img_height = $attribs["HEIGHT"]; /* ??? +$img_top; */}
		else {$img_height = $this->img_def_height; /* ??? +$img_top; */}
		if (isset($attribs["LEFT"])) {$img_left = $attribs["LEFT"];}
		else {$img_left = $this->img_def_left;}
		if (isset($attribs["BORDER"])) {$img_border = $attribs["BORDER"];}
		else {$img_border = $this->img_def_border;}
		if (isset($attribs["ALIGN"])) {$img_align = $attribs["ALIGN"];}
		else {$img_align = $this->img_def_align;}
		if (isset($attribs["WRAP"])) {$img_wrap = $attribs["WRAP"];}
		else {$img_wrap = $this->img_def_wrap;}
		if (isset($attribs["SPACE"])) {$img_space = $attribs["SPACE"];}
		else {$img_space = $this->img_def_space;}
		if (isset($attribs["ANCHOR"])) {$img_anchor = $attribs["ANCHOR"];}
		else {$img_anchor = $this->img_def_anchor;}
		//=================================================================================
		$c_image_settings = array(
									"TOP"       => $img_top,
									"WIDTH"     => $img_width,
									"HEIGHT"    => $img_height,
									"LEFT"      => $img_left,
									"BORDER"    => $img_border,
									"ALIGN"     => $img_align,
									"WRAP"      => $img_wrap,
									"SPACE"     => $img_space,
									"ANCHOR"    => $img_anchor
								);
		if (isset($this->curr_tbl_settings[$this->tbl_level]["td_y1"])) {
			$c_image_settings["TD_Y1"] = $this->curr_tbl_settings[$this->tbl_level]["td_y1"];
		}
		$this->pdf->Image($img_src, $c_image_settings, $this->pdf->x, $this->pdf->y, $img_width );
		//=================================================================================
		return;
	}// end of function
//-------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iPAGE($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			if (!$this->pdf->t_incell && !$this->pdf->in_par) {
				$this->pdf->render_line(true);
				$this->pdf->AddPage($this->pdf->CurOrientation);
			}
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------
	function _iHIDDEN($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->_add_code("");
		}
		else {
			$this->_add_code("");
		}
	} // end of function
// ------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------
	function _iCPAGENUM($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->_add_code($this->_page_curr);
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------
	function _iTPAGENUM($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->_add_code($this->_page_tot);
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iHEADER($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->p_x = $this->pdf->x;
			$this->p_y = $this->pdf->y;

			if (isset($this->pdf->c_align)) {
				$this->hf_align = $this->pdf->c_align;
			}
			else {
				$this->hf_align = $this->def_par_align;
			}
			$this->pdf->c_align = $this->header_align;
			$this->pdf->x = $this->pdf->lMargin;
			$this->pdf->y = $this->head_y;
			$this->pdf->flg_header = true;
		}
		else {
			$this->pdf->render_line(true);
			$this->pdf->c_align = $this->hf_align;
			$this->pdf->x = $this->p_x;
			$this->pdf->y = $this->p_y;
			$this->pdf->flg_header = false;
			$this->pdf->Header();
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iHEADERR($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iHEADERL($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iFOOTER($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->p_x = $this->pdf->x;
			$this->p_y = $this->pdf->y;

			if (isset($this->pdf->c_align)) {
				$this->hf_align = $this->pdf->c_align;
			}
			else {
				$this->hf_align = $this->def_par_align;
			}
			$this->pdf->c_align = $this->footer_align;
			$this->pdf->x = $this->pdf->lMargin;
			$this->pdf->y = $this->pdf->h - $this->foot_y;
			$this->pdf->flg_footer = true;
			$this->pdf->InFooter = true;
		}
		else {
			$this->pdf->render_line(true);
			$this->pdf->c_align = $this->hf_align;
			$this->pdf->x = $this->p_x;
			$this->pdf->y = $this->p_y;
			$this->pdf->flg_footer = false;
			$this->pdf->InFooter = false;
			$this->pdf->Footer();
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iFOOTERR($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iFOOTERL($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iFOOTNOTE($attribs,$EndOfTag) { //TODO
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->_in_footnote = true;
		}
		else {
			$this->_in_footnote = false;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iSECTION($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else { // END OF TAG

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iFONT($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->level++;
			$this->curr_font_settings[$this->level]["FACE"] = $this->curr_font_settings[$this->level-1]["FACE"];
			$this->curr_font_settings[$this->level]["SIZE"] = $this->curr_font_settings[$this->level-1]["SIZE"];
			$this->curr_font_settings[$this->level]["COLOR"] = $this->curr_font_settings[$this->level-1]["COLOR"];
			$this->curr_font_settings[$this->level]["STYLE"] = $this->curr_font_settings[$this->level-1]["STYLE"];
			if (isset($attribs["FACE"])) {
				$this->curr_font_settings[$this->level]["FACE"] = $this->font($attribs["FACE"]);
				$this->pdf->SetFont(
						$this->curr_font_settings[$this->level]["FACE"],
						$this->curr_font_settings[$this->level]["STYLE"],
						$this->curr_font_settings[$this->level]["SIZE"]
					);
			}
			if (isset($attribs["SIZE"])) {
				$this->curr_font_settings[$this->level]["SIZE"] = $attribs["SIZE"];
				$this->pdf->SetFontSize($attribs["SIZE"]);
			}
			if (isset($attribs["COLOR"])) {
				$this->curr_font_settings[$this->level]["COLOR"] = $attribs["COLOR"];
				$this->_set_pdf_text_color($this->curr_font_settings[$this->level]["COLOR"]);
			}
		}
		else {
			$this->level--;
			$this->pdf->SetFont(
						$this->curr_font_settings[$this->level]["FACE"],
						$this->curr_font_settings[$this->level]["STYLE"],
						$this->curr_font_settings[$this->level]["SIZE"]
					);
			$this->_set_pdf_text_color($this->curr_font_settings[$this->level]["COLOR"]);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iNEWCOL() {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iA($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iID($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iP($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->pdf->render_line(true);
			$this->pdf->in_par = true;
			$this->pdf->c_par_tab_count = 0;
			$this->pdf->par_start_y = $this->pdf->y;

			$d_before    = $this->mms($this->def_par_before);
			$d_after     = $this->mms($this->def_par_after);
			$d_align     = $this->def_par_align;
			$d_lines     = $this->mms($this->def_par_lines);
			$d_lindent   = $this->mms($this->def_par_lindent);
			$d_rindent   = $this->mms($this->def_par_rindent);
			$d_findent   = $this->mms($this->def_par_findent);

			$settings = "";
			if (isset($attribs["ALIGN"])) {
				$f_align = $this->get_align($attribs["ALIGN"]);
			}
			else { $f_align = $d_align; }
			$this->pdf->CellAlign = $f_align;
			$this->pdf->c_align = $f_align;

			if (isset($attribs["BEFORE"])) {
				$f_before = $this->mms($attribs["BEFORE"]);
			}
			else { $f_before = $d_before; }
			if (isset($attribs["AFTER"])) {
				$f_after = $this->mms($attribs["AFTER"]);
			}
			else { $f_after = $d_after; }
			if (isset($attribs["LINES"])) {
				$f_lines = $this->mms($attribs["LINES"]);
			}
			else { $f_lines = $d_lines; }
			if (isset($attribs["LINDENT"])) {
				$f_lindent = $this->mms($attribs["LINDENT"]);
			}
			else { $f_lindent = $d_lindent; }
			if (isset($attribs["RINDENT"])) {
				$f_rindent = $this->mms($attribs["RINDENT"]);
			}
			else { $f_rindent = $d_rindent; }
			if (isset($attribs["FINDENT"])) {
				$f_findent = $this->mms($attribs["FINDENT"]);
			}
			else { $f_findent = $d_findent; }

			$this->pdf->c_rindent = (float)$f_rindent;
			$this->pdf->c_lindent = (float)$f_lindent;
			$this->pdf->c_sbefore = (float)$f_before;
			$this->pdf->c_safter = (float)$f_after;
			$this->pdf->c_slines = (float)$f_lines;
			$this->pdf->c_findent = (float)$f_findent;

			// --- TABULATIONS ---------------------
			if (isset($attribs["TALIGN"])) {
				$f_talign_ar = preg_split("/[,. ]/",preg_replace("/['\"]/","",$attribs["TALIGN"]));
			}
			else { $f_talign_ar = false; }
			if (isset($attribs["LEAD"])) {
				$f_lead_ar = preg_split("/[,. ]/",preg_replace("/['\"]/","",$attribs["LEAD"]));
			}
			else { $f_lead_ar = false; }
			if (isset($attribs["TSIZE"])) {
				$f_tsize_ar = preg_split("/[,. ]/",preg_replace("/['\"]/","",$attribs["TSIZE"]));
			}
			else { $f_tsize_ar = false; }
			// -------------------------
			$this->pdf->c_par_tabs = array($f_tsize_ar,$f_talign_ar,$f_lead_ar);
			return;
			//------------------------------------------------
		}
		else { // END OF TAG
			$this->pdf->render_line(true);
			$this->pdf->in_par = false;
			$this->pdf->CellAlign = $this->def_par_align;
			$this->pdf->c_align = $this->def_par_align;
			$this->pdf->c_rindent = 0;
			$this->pdf->c_lindent = 0;
			$this->pdf->c_sbefore = 0;
			$this->pdf->c_safter = 0;
			$this->pdf->c_slines = $this->def_par_lines;
			$this->pdf->c_findent = 0;
			$this->pdf->c_string_buffer = "";
			$this->pdf->c_string_buffer2 = array();
			$this->pdf->c_par_tabs = false;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iB($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->curr_font_settings[$this->level]["STYLE"] .= "B";
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
		else {
			$this->curr_font_settings[$this->level]["STYLE"] = preg_replace("/B/i","",$this->curr_font_settings[$this->level]["STYLE"]);
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iI($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->curr_font_settings[$this->level]["STYLE"] .= "I";
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
		else {
			$this->curr_font_settings[$this->level]["STYLE"] = preg_replace("/I/i","",$this->curr_font_settings[$this->level]["STYLE"]);
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iU($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->curr_font_settings[$this->level]["STYLE"] .= "U";
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
		else {
			$this->curr_font_settings[$this->level]["STYLE"] = preg_replace("/U/i","",$this->curr_font_settings[$this->level]["STYLE"]);
			$this->pdf->SetFont(
					$this->curr_font_settings[$this->level]["FACE"],
					$this->curr_font_settings[$this->level]["STYLE"],
					$this->curr_font_settings[$this->level]["SIZE"]
				);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iBR($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->pdf->Ln();
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iSUP($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->c_fspt = $this->pdf->FontSizePt;
			$this->pdf->SetFontSize($this->c_fspt/1.5);
			$this->pdf->c_supscr = true;
		}
		else {
			$this->pdf->SetFontSize($this->c_fspt);
			$this->pdf->c_supscr = false;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iSUB($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->c_fspt = $this->pdf->FontSizePt;
			$this->pdf->SetFontSize($this->c_fspt/1.5);
			$this->pdf->c_subscr = true;
		}
		else {
			$this->pdf->SetFontSize($this->c_fspt);
			$this->pdf->c_subscr = false;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iHR($attribs,$EndOfTag) {
return;
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {

		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iTAB($attribs,$EndOfTag) {
		if ($this->_pass < 2) { return; }
		if (!$EndOfTag) {
			$this->_add_code("#%%#TAB#%%#");
		}
		else {

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iTABLE($attribs,$EndOfTag) {
		if (!$EndOfTag) {
			$this->c_table_id++;
			$this->tbl_level++;
			$this->c_table_id_a[$this->tbl_level] = $this->c_table_id;
			$this->t_c_row[$this->tbl_level] = 0;

			$this->curr_tbl_settings[$this->tbl_level] = array();
			$this->tbl_all_data_head[$this->tbl_level] = array();
			$this->tbl_all_data_body[$this->tbl_level] = array();
			$this->tbl_all_data_wdth[$this->tbl_level] = array();
			$this->tr_hd_mass[$this->tbl_level] = array();
			$this->table_data[$this->tbl_level] = array();

			if (isset($attribs["CELLPADDING"])) { $tbl_cellpadding = $this->mms($attribs["CELLPADDING"]); }
			else {$tbl_cellpadding = $this->mms($this->tbl_def_cellpadding);}
			if (isset($attribs["BORDER"])) { $tbl_border = $attribs["BORDER"];}
			else {$tbl_border = $this->tbl_def_border;}
			if (isset($attribs["WIDTH"])) { $tbl_width = $attribs["WIDTH"]; }
			else {$tbl_width = $this->tbl_def_width;}
			if (isset($attribs["ALIGN"])) { $tbl_align = $attribs["ALIGN"]; }
			else {$tbl_align = $this->tbl_def_align;}
			if (isset($attribs["VALIGN"])) { $tbl_valign = $attribs["VALIGN"]; }
			else {$tbl_valign = $this->tbl_def_valign;}
			if (isset($attribs["BGCOLOR"])) { $tbl_bgcolor = $attribs["BGCOLOR"]; }
			else {$tbl_bgcolor = $this->tbl_def_bgcolor;}
			if (isset($attribs["COLOR"])) { $tbl_bgccolor = $attribs["COLOR"]; }
			else {$tbl_bgccolor = $this->tbl_def_bgcolor;}
			if (isset($attribs["BORD_COLOR"])) {
				$brd_color = "\\brdrcf".$attribs["BORD_COLOR"]."";
			}
			else {$brd_color = "";}
			if (isset($attribs["TABLEKEEP"])) { $tbl_keep_all = "  "; }
			else {$tbl_keep_all = "";}

			if ($this->_pass == 2) {
				$this->pdf->render_line(true);
				$this->t_table_y[$this->tbl_level] = $this->pdf->y;
				$this->t_table_y_pg[$this->tbl_level] = $this->pdf->page;
				$this->t_rwspn_cnt[$this->tbl_level] = array();
				$this->c_table_fin_data[$this->tbl_level] = array();
			}

			//----------------------------------------------------------
			if (ereg("%",$tbl_width)) {
				$yyy = ereg_replace("%", "", $tbl_width);
				$this->tb_wdth[$this->tbl_level] = round((($this->pg_width - ($this->mar_left + $this->mar_right)) / 100) * $yyy);
			}
			else { $this->tb_wdth[$this->tbl_level] = $this->mms($tbl_width); }
			$cells_wdth = 0;
			$cells_hght = 0;

			$this->curr_tbl_settings[$this->tbl_level]["tbl_cellpadding"] = $tbl_cellpadding;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_border"] = $tbl_border;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_width"] = $tbl_width;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_align"] = $tbl_align;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_valign"] = $tbl_valign;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_bgcolor"] = $tbl_bgcolor;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_bgccolor"] = $tbl_bgccolor;
			$this->curr_tbl_settings[$this->tbl_level]["brd_color"] = $brd_color;
			$this->curr_tbl_settings[$this->tbl_level]["tbl_keep_all"] = $tbl_keep_all;

			$this->curr_tbl_settings[$this->tbl_level]["r_num"] = 0;
			$this->curr_tbl_settings[$this->tbl_level]["c_num"] = 0;

			$this->curr_tbl_settings[$this->tbl_level]["cells_wdth"] = 0;
		}
		else { // END OF TAG
			if ($this->_pass == 1) {
				$midle_res = $this->tbl_full(
					$this->tbl_all_data_head[$this->tbl_level],
					$this->tbl_all_data_body[$this->tbl_level],
					$this->tbl_all_data_wdth[$this->tbl_level],
					$this->curr_tbl_settings[$this->tbl_level]["cells_wdth"]
					);
			}
			if ($this->_pass == 2) {
				$tm =& $this->t_y_matrix[$this->tbl_level];
				$tm_pg =& $this->t_y_matrix_pg[$this->tbl_level];
				$keys = array_keys($tm);
				// == checking pages ============
				$latest_page = 0;
				reset($tm_pg);
				while (list ($key, $val) = each ($tm_pg)) {
					if ($latest_page<$val) {
						$latest_page = $val;
					}
				}
				//===============================
				$cc = 0; $cc_p = 0;$cc_max = 0;$fin_cc = 0;
				for ($i=0;$i<sizeof($keys);$i++) {
					$cc = $keys[$i]*1;
					if ($cc_max < $tm["".$cc.""]) {
						if (isset($tm_pg["".$cc.""]) && $latest_page == $tm_pg["".$cc.""]) {
							$cc_max = $tm["".$cc.""];$fin_cc = $cc;
						}
					}
					if ($i==0) { $cc_p = $cc; }
					$cc_p = $cc;
				}
				$this->pdf->page = $latest_page;
				$this->pdf->y = $cc_max;//+$this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];

				$this->t_y_matrix_pg[$this->tbl_level] = array();
				$this->t_y_matrix_pg_e[$this->tbl_level] = array();
				$this->t_y_matrix[$this->tbl_level] = array();
				$this->t_y_matrix_e[$this->tbl_level] = array();
				$this->t_clspn_cnt[$this->tbl_level] = array();
				$this->t_rwspn_cnt[$this->tbl_level] = array();
				//$this->t_last_w[$this->tbl_level] = array();

				$this->pdf->_draw_table_borders($this->c_table_fin_data[$this->tbl_level]);
			}
			$this->tbl_level--;
			if ($this->_pass == 2) {
				if ($this->tbl_level > 0) {
					$this->pdf->t_incell = $this->inCell[$this->tbl_level];
					$this->pdf->t_cell_w = $this->curr_tbl_settings[$this->tbl_level]["td_fin_width"];
					$this->pdf->t_cell_align = $this->get_align($this->curr_tbl_settings[$this->tbl_level]["td_align"]);
					$this->pdf->t_cell_before = $this->t_before[$this->tbl_level];
					$this->pdf->t_padding = $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
				}
			}
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iROW($attribs,$EndOfTag) {
		if (!$EndOfTag) {
			$this->c_table_td_id_a[$this->tbl_level] = 0;
			$this->t_c_row_p[$this->tbl_level] = $this->t_c_row[$this->tbl_level];
			$this->t_c_row[$this->tbl_level]++;
			if ($this->_pass == 2) {
				$this->c_table_fin_data[$this->tbl_level][$this->t_c_row[$this->tbl_level]] = array();
			}
			$this->curr_tbl_settings[$this->tbl_level]["num_t"] = 0;
			//$this->curr_tbl_settings[$this->tbl_level]["r_num"]++;
			$this->curr_tbl_settings[$this->tbl_level]["c_num"] = 0;
			$keep_row = (isset($attribs["ROWKEEP"])) ? "\\trkeep" : "";

			if (isset($attribs["CELLPADDING"])) { $tr_cellpadding = $this->mms($attribs["CELLPADDING"]); }
			else { $tr_cellpadding = $this->curr_tbl_settings[$this->tbl_level]["tbl_cellpadding"]; }
			if (isset($attribs["BORDER"])) { $tr_border = $attribs["BORDER"]; }
			else { $tr_border = $this->curr_tbl_settings[$this->tbl_level]["tbl_border"]; }
			if (isset($attribs["WIDTH"])) { $tr_width = $attribs["WIDTH"]; }
			else {$tr_width = $this->curr_tbl_settings[$this->tbl_level]["tbl_width"];}
			if (isset($attribs["HEIGHT"])) { $tr_height = $attribs["HEIGHT"]; }
			else {$tr_height = 0;}
			if (isset($attribs["ALIGN"])) { $tr_align = $attribs["ALIGN"]; }
			else {$tr_align = $this->row_def_align;}
			if (isset($attribs["VALIGN"])) { $tr_valign = $attribs["VALIGN"]; }
			else { $tr_valign = $this->curr_tbl_settings[$this->tbl_level]["tbl_valign"]; }
			if (isset($attribs["BGCOLOR"])) { $tr_bgcolor = $attribs["BGCOLOR"]; }
			else { $tr_bgcolor = $this->curr_tbl_settings[$this->tbl_level]["tbl_bgcolor"]; }
			if (isset($attribs["COLOR"])) { $tr_bgccolor = $attribs["COLOR"]; } // TODO
			else { $tr_bgccolor = $this->curr_tbl_settings[$this->tbl_level]["tbl_bgccolor"]; }
			if (isset($attribs["HEADING"])) { $tr_header = "\\trhdr"; }
			else { $tr_header = ""; }

			///////////////////// - ROW
			if (ereg("%",$tr_width)) {
				$yyy = ereg_replace("%", "", $tr_width);
				$tr_twips_wdth = round((($this->pg_width - ($this->mar_left + $this->mar_right)) / 100) * $yyy);
			}
			else { $tr_twips_wdth = $this->mms($tr_width); }
			if ($tr_height!=0){ $tr_twips_height = "\\trrh".$this->mms($tr_height); }
			else {$tr_twips_height = "\\trrh100"; }

			switch ($this->curr_tbl_settings[$this->tbl_level]["tbl_align"]) {
				case "CENTER": $tbl_all_all = "\\trqc "; break;
				case "LEFT": $tbl_all_all = "\\trql "; break;
				case "RIGHT": $tbl_all_all = "\\trqr "; break;
				default: $tbl_all_all = ""; break;
			}
			//----
			$tr_padding = "\\trpaddl".$tr_cellpadding."\\trpaddt".$tr_cellpadding."\\trpaddb".$tr_cellpadding."\\trpaddr".$tr_cellpadding."\\trpaddfl3\\trpaddft3\\trpaddfb3\\trpaddfr3";
			$tr_res = "\\pard\\trowd".$keep_row.$tr_header.$tbl_all_all.$tr_padding."\\trgaph100".$tr_twips_height."\\trleft36\r\n";
			//----
			$this->tr_hd_mass[$this->tbl_level][] = $tr_res;
			////////////////
			$cells_row_hght = 0;
			$cells_row_wdth = 0;

			$this->curr_tbl_settings[$this->tbl_level]["tr_border"] = $tr_border;
			$this->curr_tbl_settings[$this->tbl_level]["tr_align"] = $tr_align;
			$this->curr_tbl_settings[$this->tbl_level]["tr_valign"] = $tr_valign;
			$this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"] = $tr_cellpadding;
			$this->curr_tbl_settings[$this->tbl_level]["tr_bgcolor"] = $tr_bgcolor;
			$this->curr_tbl_settings[$this->tbl_level]["tr_bgccolor"] = $tr_bgccolor;
			$this->curr_tbl_settings[$this->tbl_level]["tr_twips_wdth"] = $tr_twips_wdth;
			$this->curr_tbl_settings[$this->tbl_level]["tr_twips_height"] = $tr_twips_height;

			$this->curr_tbl_settings[$this->tbl_level]["cells_row_wdth"] = 0;

			if ($this->_pass > 1) {
				$tbl_align = $this->curr_tbl_settings[$this->tbl_level]["tbl_align"];
				if ($this->tbl_level == 1) {
					$this->t_before[$this->tbl_level] = 0;
					$table_fin_width = array_sum($this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]]);
					$available_width = $this->pdf->w - $this->pdf->rMargin - $this->pdf->lMargin;

				}
				else {
					$this->t_before[$this->tbl_level] = $this->t_before[$this->tbl_level-1]+$this->curr_tbl_settings[$this->tbl_level-1]["tr_cellpadding"];
					$table_fin_width = array_sum($this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]]);
					$available_width = $this->curr_tbl_settings[$this->tbl_level-1]["td_fin_width"]-
										$this->curr_tbl_settings[$this->tbl_level-1]["tr_cellpadding"]*2;

				}

				$adj = false;
				if ($tbl_align == "CENTER") {
					$adj = ($available_width - $table_fin_width) / 2;
				}
				else if ($tbl_align == "RIGHT") {
					$adj = $available_width - $table_fin_width;
				}
				if ($adj) {
					$this->t_before[$this->tbl_level] += $adj;
				}
			}

		}
		else { // END OF TAG
			if ($this->_pass == 2) {
				$this->t_y_matrix[$this->tbl_level] = $this->t_y_matrix_e[$this->tbl_level];
				$this->t_y_matrix_pg[$this->tbl_level] = $this->t_y_matrix_pg_e[$this->tbl_level];
				//======== down row spans ===============================
				$trsp =& $this->t_rwspn_cnt[$this->tbl_level];
					reset($trsp);
					while (list ($key, $val) = each ($trsp)) {
						$trsp[$key]--;
						if ($trsp[$key]<0) {
							$trsp[$key] = 0;
						}
					}
				//=======================================================
			}
			if ($this->curr_tbl_settings[$this->tbl_level]["cells_wdth"]<$this->curr_tbl_settings[$this->tbl_level]["cells_row_wdth"]) {
				$this->curr_tbl_settings[$this->tbl_level]["cells_wdth"]=$this->curr_tbl_settings[$this->tbl_level]["cells_row_wdth"];
			}
			$this->curr_tbl_settings[$this->tbl_level]["r_num"]++;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iCELL($attribs,$EndOfTag) {
		if (!$EndOfTag) {

			if (isset($attribs["COLSPAN"])) { $td_colspan = $attribs["COLSPAN"]; }
			else {$td_colspan =1;}
			if (isset($attribs["ROWSPAN"])) { $td_rowspan = $attribs["ROWSPAN"];  }
			else {$td_rowspan =1;}
			if (isset($attribs["BORDER"])) { $td_border = $attribs["BORDER"];  }
			else {$td_border = $this->curr_tbl_settings[$this->tbl_level]["tr_border"];}
			if (isset($attribs["WIDTH"])) { $td_width = $attribs["WIDTH"]; }
			else {$td_width = "no";}
			if (isset($attribs["ALIGN"])) { $td_align = $attribs["ALIGN"]; }
			else {$td_align = $this->curr_tbl_settings[$this->tbl_level]["tr_align"];}
			if (isset($attribs["VALIGN"])) { $td_valign = $attribs["VALIGN"]; }
			else {$td_valign = $this->curr_tbl_settings[$this->tbl_level]["tr_valign"];}
			if (isset($attribs["BGCOLOR"])) { $td_bgcolor = $attribs["BGCOLOR"]; }
			else {$td_bgcolor = $this->curr_tbl_settings[$this->tbl_level]["tr_bgcolor"];}
			if (isset($attribs["COLOR"])) { $td_bgccolor = $attribs["COLOR"]; }
			else {$td_bgccolor = $this->curr_tbl_settings[$this->tbl_level]["tr_bgccolor"];}
			$brd_color = $this->curr_tbl_settings[$this->tbl_level]["brd_color"];

			if (ereg("%",$td_width)) {
				$ooo = ereg_replace("%", "", $td_width);
				$td_wdth_mass[] = round(($this->curr_tbl_settings[$this->tbl_level]["tr_twips_wdth"] / 100) * $ooo);
				$tmp_wdth = round(($this->curr_tbl_settings[$this->tbl_level]["tr_twips_wdth"] / 100) * $ooo);
			}
			else if ($td_width=="no") {$td_wdth_mass[] = "no"; $tmp_wdth = "no"; }
			else { $td_wdth_mass[] = $this->mms($td_width); $tmp_wdth = $this->mms($td_width); }

			$tmp_head = " ";

			$this->curr_tbl_settings[$this->tbl_level]["td_colspan"] = $td_colspan;
			$this->curr_tbl_settings[$this->tbl_level]["td_rowspan"] = $td_rowspan;
			$this->curr_tbl_settings[$this->tbl_level]["td_align"] = $td_align;
			$this->curr_tbl_settings[$this->tbl_level]["tmp_head"] = $tmp_head;
			$this->curr_tbl_settings[$this->tbl_level]["tmp_wdth"] = $tmp_wdth;

			if ($this->_pass == 2) {
				$fin_td_w = 0;
				$this->_get_cell_before();

				$cc_row =& $this->c_table_fin_data[$this->tbl_level][$this->t_c_row[$this->tbl_level]];

				if (isset($td_colspan) && $td_colspan > 1) {
					for ($i=0;$i<$td_colspan;$i++) {
						$fin_td_w += $this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]][$this->c_table_td_id_a[$this->tbl_level]+$i];
					}
					$this->c_table_td_id_a[$this->tbl_level] += $td_colspan;
					$this->t_clspn_cnt[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = $td_colspan;
				}
				else {
					$fin_td_w = $this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]][$this->c_table_td_id_a[$this->tbl_level]];
					$this->c_table_td_id_a[$this->tbl_level]++;
					$this->t_clspn_cnt[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = 0;
				}

				$cc_row[$this->c_table_td_id_a[$this->tbl_level]] = array();
				$cc_cell =& $cc_row[$this->c_table_td_id_a[$this->tbl_level]];

				$this->inCell[$this->tbl_level] = true;

				// ============================
				$this->pdf->t_incell = true;
				$this->pdf->t_padding = $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
				$this->pdf->t_cell_w = $fin_td_w;
				$this->pdf->t_cell_align = $this->get_align($td_align);
				$this->pdf->t_cell_before = $this->t_before[$this->tbl_level];

				if (isset($td_bgccolor) && $td_bgccolor) {
					$cc_cell["BGCOLOR"] = $this->get_pdf_color($td_bgccolor);
				}
				if (isset($td_border)) { $cc_cell["BORDER"] = $td_border; }
				$cc_cell["PADDING"] = $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
				$cc_cell["FIN_WIDTH"] = $fin_td_w;
				$cc_cell["TD_X1"] = $this->t_before[$this->tbl_level];
				$cc_cell["TD_X2"] = $this->t_before[$this->tbl_level]+$fin_td_w;
				$this->curr_tbl_settings[$this->tbl_level]["td_fin_width"] = $fin_td_w;
				$this->curr_tbl_settings[$this->tbl_level]["td_x1"] = $this->t_before[$this->tbl_level];
				$this->curr_tbl_settings[$this->tbl_level]["td_x2"] = $this->t_before[$this->tbl_level]+$fin_td_w;

				if (isset($td_rowspan) && $td_rowspan>1) {
					$cc_cell["ROWSPAN"] = $td_rowspan-1;
					$this->t_rwspn_cnt[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = $td_rowspan;
					if (isset($td_colspan) && $td_colspan > 1) {
						for ($i=1;$i<$td_colspan;$i++) {
							$tmp_td_w = $this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]][$this->c_table_td_id_a[$this->tbl_level]+$i];
							$this->t_rwspn_cnt[$this->tbl_level]["".$this->t_before[$this->tbl_level]+$tmp_td_w.""] = $td_rowspan;
						}
					}
				}
				else {
					$this->t_rwspn_cnt[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = 0;
				}
				$t_y = $this->_get_cell_y();
				if ($t_y > 0) {
					$this->pdf->y = $t_y + $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
					$cc_cell["TD_Y1"] = $this->pdf->y;
					$this->curr_tbl_settings[$this->tbl_level]["td_y1"] = $this->pdf->y;

				}
				else {
					$this->pdf->y = $this->t_table_y[$this->tbl_level] + $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
					$this->pdf->page = $this->t_table_y_pg[$this->tbl_level];
					$cc_cell["TD_Y1"] = $this->pdf->y;
					$this->curr_tbl_settings[$this->tbl_level]["td_y1"] = $this->pdf->y;
				}
				// ============================
				$this->t_last_w[$this->tbl_level] = $fin_td_w;
				$cc_cell["P1"] = $this->pdf->page;

			}

		}
		else { // END OF CELL TAG

			if ($this->_pass == 2) {
				$cc_cell =& $this->c_table_fin_data[$this->tbl_level][$this->t_c_row[$this->tbl_level]][$this->c_table_td_id_a[$this->tbl_level]];
				$this->pdf->render_line(true);
				$this->pdf->y = $this->pdf->y + $this->curr_tbl_settings[$this->tbl_level]["tr_cellpadding"];
				$this->t_y_matrix_e[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = $this->pdf->y;
				$cc_cell["TD_Y2"] = $this->pdf->y;
				$cc_cell["P2"] = $this->pdf->page;

				$this->curr_tbl_settings[$this->tbl_level]["td_y2"] = $this->pdf->y;
				$this->t_y_matrix_pg_e[$this->tbl_level]["".$this->t_before[$this->tbl_level].""] = $this->pdf->page;
				$this->t_before[$this->tbl_level] += $this->t_last_w[$this->tbl_level];

				// =====================================================
				$this->inCell[$this->tbl_level] = false;
				// ============================
				$this->pdf->t_incell = false;
				$this->pdf->t_cell_w = 0;
				// ============================
			}

			for ($gh=0;$gh<$this->curr_tbl_settings[$this->tbl_level]["td_rowspan"];$gh++) {
				for ($jh=0;$jh<$this->curr_tbl_settings[$this->tbl_level]["td_colspan"];$jh++) {
					$this->tbl_all_data_head[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["num_t"]][$gh][$jh] = $this->curr_tbl_settings[$this->tbl_level]["tmp_head"];
					$this->tbl_all_data_body[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["num_t"]][$gh][$jh] = " ";//$tmp_body;
					$this->tbl_all_data_wdth[$this->tbl_level][$this->curr_tbl_settings[$this->tbl_level]["r_num"]][$this->curr_tbl_settings[$this->tbl_level]["num_t"]][$gh][$jh] = $this->curr_tbl_settings[$this->tbl_level]["tmp_wdth"];
				}
			}
			$this->curr_tbl_settings[$this->tbl_level]["num_t"]++;
			$this->curr_tbl_settings[$this->tbl_level]["cells_row_wdth"]++;
			if ($this->curr_tbl_settings[$this->tbl_level]["td_colspan"]>1) {
				$this->curr_tbl_settings[$this->tbl_level]["cells_row_wdth"]+=$this->curr_tbl_settings[$this->tbl_level]["td_colspan"]-1;
			}
			//----------------------------------------------------------

			$this->curr_tbl_settings[$this->tbl_level]["c_num"]++;

		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _get_cell_before() {
		$trsp =& $this->t_rwspn_cnt[$this->tbl_level];
		while (isset($trsp["".$this->t_before[$this->tbl_level].""]) && $trsp["".$this->t_before[$this->tbl_level].""] > 0) {
			$this->t_before[$this->tbl_level] += $this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]][$this->c_table_td_id_a[$this->tbl_level]];
			$this->c_table_td_id_a[$this->tbl_level]++;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _get_cell_y() {
		$tm =& $this->t_y_matrix[$this->tbl_level];
		$tm_pg =& $this->t_y_matrix_pg[$this->tbl_level];
		$trsp =& $this->t_rwspn_cnt[$this->tbl_level];
		$cur_page = $this->pdf->page;
		if (sizeof($tm) == 0) {
			return 0;
		}
		$cb = $this->t_before[$this->tbl_level];
		$keys = array_keys($tm);
		// == checking pages ============
		$latest_page = 0;$need_page = false;
		reset($tm_pg);
		while (list ($key, $val) = each ($tm_pg)) {
			if ($latest_page<$val) {
				$latest_page = $val;
			}
			if ($key >= $cb && $key < $cb+$this->pdf->t_cell_w && $need_page < $val) {
				$need_page = $val;
			}
			else if ($need_page === false || $need_page > $val) {
				$need_page = $val;
			}
		}
		//===============================
		$cc = 0; $cc_p = 0;$cc_max = 0;$fin_cc = 0;
		for ($i=0;$i<sizeof($keys);$i++) {
			$cc = $keys[$i]*1;
			if ($cc_max < $tm["".$cc.""]) {
				if (isset($tm_pg["".$cc.""]) && $need_page <> $tm_pg["".$cc.""]) {
					//
				}
				else {
					if (isset($trsp["".$cc.""]) && $trsp["".$cc.""] > 0) {
						//
					}
					else {$cc_max = $tm["".$cc.""];$fin_cc = $cc;}
				}

			}
			if ($i==0) { $cc_p = $cc; }
			$cc_p = $cc;
		}
		if ($cc_max > 0) {
			if (isset($tm_pg["".$fin_cc.""])) {
				$this->pdf->page = $tm_pg["".$fin_cc.""];
			}
			return $cc_max;
		}
		if (isset($tm["".$cc_p+$this->pdf->t_cell_w.""])) {
			if (isset($tm_pg["".$cc_p+$this->pdf->t_cell_w.""])) {
				$this->pdf->page = $tm_pg["".$cc_p+$this->pdf->t_cell_w.""];
			}
			return $tm["".$cc_p+$this->pdf->t_cell_w.""];
		}
		if (isset($tm["".$cc_p.""])) {
			if (isset($tm_pg[$cc_p])) {
				$this->pdf->page = $tm_pg["".$cc_p.""];
			}
			return $tm["".$cc_p.""];
		}
		return 0;
	} // end of function
// ------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------
	function _process_attribs($attr) {
		if (isset($attr["COLOR"])) {
			//$this->add_color($attr["COLOR"]);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function get_pdf_color($color) {
		$color = preg_replace("/\#/","",$color);
		$r = hexdec(substr($color, 0, 2));
		$g = hexdec(substr($color, 2, 2));
		$b = hexdec(substr($color, 4, 2));
		//\red0\green0\blue0;
		return array($r,$g,$b);
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _set_pdf_text_color($color) {
		$color = preg_replace("/\#/","",$color);
		$r = hexdec(substr($color, 0, 2));
		$g = hexdec(substr($color, 2, 2));
		$b = hexdec(substr($color, 4, 2));
		//\red0\green0\blue0;
		$this->pdf->SetTextColor($r,$g,$b);
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
	function file_check($file) {
		if (!file_exists($file)) { die("<b>Wrong path to the settings file - doc_config.inc. <br>Script is terminated</b>"); return false; }
		else { return true; }
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function process_images() {
		if ($this->temp_dir !== false) {
			$test_fin = preg_split("/".$this->image_token."/ms",$this->text);

			// trying to save memory by using tmp file
			$tmp_file_name = $this->rnd_proc_nm."_final";
			$fp = fopen($this->temp_dir.$tmp_file_name, "w");

			fwrite($fp, $this->header."\r\n");
			fwrite($fp, $test_fin[0]);
			if (sizeof($test_fin)>1) {
				for ($i=1;$i<sizeof($test_fin);$i++) {
					preg_match("/(\d+)nort/",$test_fin[$i],$mtchs);
					$img_num = $mtchs[1];
					unset($mtchs);
					$test_fin[$i] = preg_replace("/".$img_num."nort"."/","",$test_fin[$i]);

					//read the contents of a tmt image data into the final tmp file
					$handle = fopen ($this->temp_dir.$this->rnd_proc_nm."_".$img_num, "rb");
					while (!feof($handle)) {
						$data = fread($handle, 8192);
						fwrite($fp, $data);
						empty($data);
					}
					fclose ($handle);
					@unlink($this->temp_dir.$this->rnd_proc_nm."_".$img_num);
					fwrite($fp, $test_fin[$i]);
					empty($test_fin[$i]);
				}
			}
			fwrite($fp, "\r\n}");
			@fclose($fp);
		}
		else {
			if (preg_match_all("/".$this->image_token."/ms", $this->text,$count_i)) {
				$count_i = $count_i[0];
				for ($i=0;$i<sizeof($count_i);$i++) {
					$fnd = $this->image_token.$i."nort";
					$this->text = ereg_replace($fnd,$this->image_array[$i],$this->text);
				}
			}
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
	function pixtotwips($pix) {
		return $this->mms($pix * 3.53);
	}// end of function
//-------------------------------------------------------------------------------------------------
	function openimage($image) {
		$sz = 0;$cy = "";
			$fp = @fopen($image, "rb");
			if (!$fp) {
				return false;
			}
			while (!feof($fp)){
				$cy .= @fread($fp, 1024);
				$sz++;
				if ($sz > $this->image_size) { break; }
			}
			@fclose($fp);
			return bin2hex($cy);
	}// end of function
//-------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function render_doc() {
		//--- PROCESSING COLORS ----------------
		$colors = "";
		reset ($this->color_table);
		while (list ($key, $val) = each ($this->color_table)) {
			$colors .= $this->color_table[$key]["VALUE"];
		}
		$this->header = preg_replace("/goesuserscolors/",$colors,$this->header);
		//--------------------------------------
		$this->process_images();
		//--------------------------------------
		$this->text = $this->header."\r\n".$this->text."\r\n}";
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _iGetCode() {
		if ($this->temp_dir !== false) {
			@unlink($this->temp_dir.$this->rnd_proc_nm."_final");
			trigger_error ("If you are using temporary directory you need to call either <b>get_doc_stream(\"file_name\");</b> or <b>get_doc_to_file(\"path_to_file\",\"file_name\");</b>. Please, consult documentation for additional information.<br>", E_USER_ERROR);
			exit;
		}
		else {
			//$this->render_doc();
		}
		return $this->pdf->Output("","S");
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _get_doc_stream() {
		$this->render_doc();
		$handle = fopen ($this->temp_dir.$this->rnd_proc_nm."_final", "rb");
		do {
			$data = fread($handle, 8192);
			if (strlen($data) == 0) break;
			echo $data;
			empty($data);
		} while(true);
		fclose ($handle);
		@unlink($this->temp_dir.$this->rnd_proc_nm."_final");
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _get_doc_to_file($path,$file_name) {
		$this->render_doc();

		if (!copy($this->temp_dir.$this->rnd_proc_nm."_final", $path.$file_name)) {
			trigger_error ("Failed to copy the file to the given destination : '".$path.$file_name."'<br>\n", E_USER_ERROR);
		}

		@unlink($this->temp_dir.$this->rnd_proc_nm."_final");
	} // end of function
// ------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
////////////////////// three dimensional array parse
//-------------------------------------------------------------------------------------------------
	function tbl_full($mass_head,$mass_body,$mass_wdth,$width) {
		$shablon_mass = array();
		$fin_tbl_head = array();
		$fin_tbl_body = array();
		$fin_tbl_wdth = array();
		$h = false;
		$hhh = false;
		$hh = "no";

		for ($i=0;$i<sizeof($mass_wdth);$i++) {
			for ($b=0;$b<$width;$b++){
				$shablon_mass[$i][$b] = "&nbsp;";
				$fin_tbl_head[$i][$b] = $hhh;
				$fin_tbl_body[$i][$b] = $h;
				$fin_tbl_wdth[$i][$b] = $hh;
			}
		}
		$num_id = 0;
		for ($a=0;$a<sizeof($mass_wdth);$a++)
		{
			$id = 0; // номер блока в ряду
			for ($c=0;$c<$width;$c++) {
				if ($fin_tbl_body[$a][$c]==$h) {
						for ($lk=0;$lk<sizeof(@$mass_wdth[$a][$id]);$lk++) {
							for ($kl=0;$kl<sizeof($mass_wdth[$a][$id][$lk]);$kl++) {
								if ($mass_wdth[$a][$id][$lk][$kl]!="") {
									$shablon_mass[$a+$lk][$c+$kl] = $num_id+$id+1;
									$fin_tbl_head[$a+$lk][$c+$kl] = $mass_head[$a][$id][$lk][$kl];
									$fin_tbl_body[$a+$lk][$c+$kl] = $mass_body[$a][$id][$lk][$kl];
									$fin_tbl_wdth[$a+$lk][$c+$kl] = $mass_wdth[$a][$id][$lk][$kl];
								}
							}
						}
					$id++; // $num_id += $id;
				}
			}
			$num_id += $id;
		}
		$fin_max = $this->row_me($fin_tbl_wdth,$width,$shablon_mass);

		$this->table_fin_wdth[$this->c_table_id_a[$this->tbl_level]] = $fin_max;
		// may be we will need to return only $fin_max for each table
		// for pass 1. This process should be omited in pass 2
		//return $this->final_parse($fin_tbl_head,$fin_max,$fin_tbl_body,$shablon_mass);
	}// end of function
//-------------------------------------------------------------------------------------------------
//////////////  object inserted tables searching
//-------------------------------------------------------------------------------------------------
	function obj_srch($shablon) {
		$width = sizeof($shablon[0]);
		$height = sizeof($shablon);
		for ($h=0;$h<$height;$h++) {
			$g_count=0;
			for ($w=0;$w<$width;$w++) {
				if ($shablon[$h][$w] != $shablon[$h+1][$w]) { $g_count++; }
			}
			$g_mass[$h] = $g_count;
		}
		for ($w=0;$w<$width;$w++) {
			$v_count=0;
			for ($h=0;$h<$height;$h++) {
				if ($shablon[$h][$w] != $shablon[$h][$w+1]) { $v_count++; }
			}
			$v_mass[$w] = $v_count;
		}
	}
//-------------------------------------------------------------------------------------------------
////////////////////// crow widths counting function
//-------------------------------------------------------------------------------------------------
	function row_me($wdth,$or_wdth,$shablon) {
		for ($h=0;$h<sizeof($wdth);$h++) {
			$count = 0; $sum = 0; $mstc = 0;
			for ($w=0;$w<$or_wdth;$w++) {
				if (isset($wdth[$h][$w]) && $wdth[$h][$w] == "no") { $count++; }
				else {
					if (@$shablon[$h][$w] != @$shablon[$h][$w+1]) {
						$sum += $wdth[$h][$w];
						$wdth[$h][$w] = $wdth[$h][$w]."mst".$mstc; $mstc = 0;
					}
					else { @$wdth[$h][$w] = @$wdth[$h][$w]."sl".$mstc; $mstc++;}
				}
			}
			if ($count == 0) {$count = 1;}
			$opt = round(($this->tb_wdth[$this->tbl_level] - $sum) / $count,3);
			for ($w=0;$w<$or_wdth;$w++) {
				if ($wdth[$h][$w] == "no") { $wdth[$h][$w] = $opt; }
			}
		}
		for ($w=0;$w<$or_wdth;$w++) {
			$fl=false;
			for ($h=0;$h<sizeof($wdth);$h++) {
				if (ereg("mst",$wdth[$h][$w]) || ereg("sl",$wdth[$h][$w])) { $fl = true; }
			}
			if ($fl) { $yes_no[$w] = "yes"; }
			else { $yes_no[$w] = "no"; }
		}
		return $this->mxs($wdth,$or_wdth,$shablon);
	}// end of function
//-------------------------------------------------------------------------------------------------
////////////////////// main borders counting function
//-------------------------------------------------------------------------------------------------
	function mxs($wdth,$or_wdth,$shablon) {
		$t_count = 0; $mst = array();$fin_max = array();
		for ($h=0;$h<$or_wdth;$h++) { $fin_max[$h]="no"; }
		for ($w=0;$w<$or_wdth;$w++) {
			for ($h=0;$h<sizeof($wdth);$h++) {
				$d_tmp = 0;
				if (ereg("mst",$wdth[$h][$w])) {
					$width = preg_replace("/mst\d+/","",$wdth[$h][$w]);
					$span = preg_replace("/\d+mst/","",$wdth[$h][$w]);
					if ($span>0) {
						$tty = $width / ($span + 1);
						//TODO
						if (isset($mst_mass[$w]) && $mst_mass[$w]<$tty) { $mst_mass[$w] = $tty; $mst[$w] = $wdth[$h][$w]; }
					}
					else {
						$d_tmp = $width;
					}
				}
				if ($fin_max[$w]<$d_tmp || $fin_max[$w] == "no") { $fin_max[$w] = $d_tmp; }
			}
			$t_count++;
		}
		for ($i=0;$i<$t_count;$i++) { if ($fin_max[$i] == "") { $fin_max[$i] = "no"; } }
		return $this->mxs2($fin_max,$mst);
	}
//-------------------------------------------------------------------------------------------------
	function mxs2($fin_max,$mst) {
		for ($i=0;$i<sizeof($fin_max);$i++) {
			$tmp_sum = 0; $fl = 1;
			if (isset($mst[$i]) &&  $mst[$i] != "") {
				if ($fin_max[$i] == "no") {
					$width = preg_replace("/mst\d+/","",$mst[$i]);
					$span = preg_replace("/\d+mst/","",$mst[$i]);
					for ($h=$i-$span;$h<$i;$h++) {
						if ($fin_max[$h] != "no") { $tmp_sum += $fin_max[$h]; }
						else { $fl++; }
					}
					$opt = round(($width - $tmp_sum) / $fl,3);
					for ($h=$i-$span;$h<=$i;$h++) {
						if ($fin_max[$h] == "no") { $fin_max[$h] = $opt; }
					}
				}
				else {
					$width = preg_replace("/mst\d+/","",$mst[$i]);
					$span = preg_replace("/\d+mst/","",$mst[$i]);
					for ($h=$i-$span;$h<=$i;$h++) {
						if ($fin_max[$h] != "no") { $tmp_sum += $fin_max[$h]; }
						else { $fl++; }
					}
					$opt = round(($width - $tmp_sum) / ($fl - 1),3);
					if ($opt>=0) {
						for ($h=$i-$span;$h<$i;$h++) {
							if ($fin_max[$h] == "no") { $fin_max[$h] = $opt; }
						}
					}
				}
			}
		}
		$f_sum = 0; $f_fl = 0;
		for ($i=0;$i<sizeof($fin_max);$i++) {
			if ($fin_max[$i] != "no") { $f_sum += $fin_max[$i]; }
			else { $f_fl++; }
		}
		$f_fl = ($f_fl == 0) ? 1 : $f_fl;
		$f_opt = round(($this->tb_wdth[$this->tbl_level] - $f_sum) / $f_fl,3); // TODO
		//$f_opt = floor(($this->tb_wdth[$this->tbl_level] - $f_sum) / $f_fl);
		if ($f_opt<0) { $f_opt = 10; }
		for ($i=0;$i<sizeof($fin_max);$i++) {
			if ($fin_max[$i] == "no") { $fin_max[$i] = $f_opt; }
		}
		return $fin_max;
	}
//////////////////////////////////////////////////////////
//-------------------------------------------------------------------------------------------------



} // END OF CLASS
//-------------------------------------------------------------------------------------------------


?>