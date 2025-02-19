<?php

// v.0.1      [08.08.2007]
//         www.paggard.com

// ------------------------------------------------------------------------------------------------
class PDF extends FPDF {

	var $iHeader;
	var $iFooter;
	var $flg_header=false;
	var $flg_footer=false;
	
	var $CellAlign;
	
	// ---------------------------------
	var $c_string_length = false;
	var $c_string_length_max = false;
	var $c_string_buffer = false;
	var $c_string_buffer2 = array();
	var $c_string_actual = false;
	var $c_line_width = 0;
	var $c_align;
	var $c_rindent = 0;
	var $c_lindent = 0;
	var $c_findent = 0;
	var $in_par = false;
	var $c_sbefore = 0;
	var $c_safter = 0;
	var $c_slines = 0;
	var $par_lines = 0;
	var $par_start_y = 0;
	var $c_par_tabs;
	var $c_par_tab_count;
	
	var $line_align;
	
	var $c_font_pdf;

	var $c_subscr = false;
	var $c_supscr = false;

	var $t_incell = false;
	var $t_padding;
	var $t_cell_w;
	var $t_cell_align;
	var $t_cell_before;
	
	var $wrap_borders = array();
	// ---------------------------------
	
// ------------------------------------------------------------------------------------------------
	function Header() {
		if ($this->iHeader != "") {
			$this->_out($this->iHeader);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function Footer() {
		if ($this->iFooter != "") {
			$this->_out($this->iFooter);
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _out($s) {
		if ($this->flg_header) {
			$this->iHeader .= $s."\n";
			return;
		}
		if ($this->flg_footer) {
			$this->iFooter .= $s."\n";
			return;
		}
		//Add a line to the document
		if($this->state==2)
			$this->pages[$this->page].=$s."\n";
		else
			$this->buffer.=$s."\n";
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
		$k=$this->k;
		if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
		{
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			{
					$this->ws=0;
					$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			{
					$this->ws=$ws;
					$this->_out(sprintf('%.3f Tw',$ws*$k));
			}
		}
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$s='';
		if($fill==1 or $border==1)
		{
			if($fill==1)
					$op=($border==1) ? 'B' : 'f';
			else
					$op='S';
			$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		if(is_string($border))
		{
			$x=$this->x;
			$y=$this->y;
			if(is_int(strpos($border,'L')))
					$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'T')))
					$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(is_int(strpos($border,'R')))
					$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'B')))
					$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		}
		if($txt!='')
		{
			if ($align=="") {
				$align = $this->CellAlign;
			}
			if($align=='R')
					$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
			elseif($align=='C')
					$dx=($w-$this->GetStringWidth($txt))/2;
			elseif($align=='FJ')
			{
					//Set word spacing
					$wmax=($w-2*$this->cMargin);
					if (substr_count($txt,' ') == 0) {
						$this->ws=($wmax-$this->GetStringWidth($txt));
					}
					else {$this->ws=($wmax-$this->GetStringWidth($txt))/substr_count($txt,' ');}
					
					$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
					$dx=$this->cMargin;
			}
			else {
					$dx=$this->cMargin;
			}
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			if($this->ColorFlag)
					$s.='q '.$this->TextColor.' ';
			$s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt);
			if($this->underline)
					$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
					$s.=' Q';
			if($link)
			{
					if($align=='FJ')
						$wlink=$wmax;
					else
						$wlink=$this->GetStringWidth($txt);
					$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$wlink,$this->FontSize,$link);
			}
		}
		if($s)
			$this->_out($s);
		if($align=='FJ')
		{
			//Remove word spacing
			$this->_out('0 Tw');
			$this->ws=0;
		}
		$this->lasth=$h;
		if($ln>0)
		{
			$this->y+=$h;
			if($ln==1)
					$this->x=$this->lMargin;
		}
		else
			$this->x+=$w;
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _get_line_buff_size() {
		$tmp_string = "";
		$tmp_totsize = 0;
		$tmp_totsize_n = 0;
		if ($this->c_string_buffer2 && sizeof($this->c_string_buffer2)>0) {
			$chunks = sizeof($this->c_string_buffer2);
			for ($i=0;$i<$chunks;$i++) {
				$val = $this->c_string_buffer2[$i];
				if ($i==0) {
					$bef = strlen($val["TXT"]);
					$val["TXT"] = ltrim($val["TXT"]);
					$aft = strlen($val["TXT"]);
					if ($aft < $bef) {
						$sps = $val["SPS"] * ($bef-$aft);
						$val["SIZE"] = $val["SIZE"] - ($sps*$val["FONT_SIZE"]/1000);
					}
				}
				if ($i==($chunks-1)) {
					$bef = strlen($val["TXT"]);
					$val["TXT"] = rtrim($val["TXT"]);
					$aft = strlen($val["TXT"]);
					if ($aft < $bef) {
						$sps = $val["SPS"] * ($bef-$aft);
						$val["SIZE"] = $val["SIZE"] - ($sps*$val["FONT_SIZE"]/1000);
					}
				}
				$tmp_string .= $val["TXT"];
				$tmp_totsize += $val["SIZE"]*1000/$val["FONT_SIZE"];
				$tmp_totsize_n += $val["SIZE"];
			}
		}
		else { return array(0,0); }
		return array($tmp_totsize,$tmp_totsize_n);
	} // end of function
// ------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------
	function myWrite2($h,$txt,$link='') {
		if ($this->c_slines == 0) {
			$this->c_slines = $h;
		}

		$tab_size = 10;
		$line_start = 0;
		$line_end = 0;
		if ($this->par_lines == 0) {
			$y_adj = $this->c_sbefore+$this->c_slines;
		}
		else {$y_adj = 0;}

		$cw=&$this->CurrentFont['cw'];

		//=====================================
		if ($this->t_incell) {
			$w2 = $this->t_cell_w - ($this->t_padding*2) - $this->c_rindent - $this->c_lindent;
			$line_start = $line_start + $this->c_lindent + $this->t_padding + $this->t_cell_before + $this->lMargin;
			$line_end = $line_start + $this->t_cell_w - $this->t_padding;
		}
		else {
			$w2 = $this->w - $this->rMargin - $this->lMargin - $this->c_rindent - $this->c_lindent;
			$line_start = $line_start + $this->lMargin + $this->c_lindent;
			$line_end = $this->w - $this->rMargin - $this->c_rindent;
		}
		if ($this->c_line_width == 0) {
			$this->c_string_buffer = "";
		}
		else {
			$w2 = $w2 - $this->c_line_width;
		}
		if ($this->par_lines == 0 && $this->c_findent<>0) {
			$w2 = $w2 - ($this->c_findent);
			$line_start = $line_start + $this->c_findent;
		}

		// --- TAB for the first line ----
		if ($this->par_lines == 0 && $this->c_findent<>0) {
			if ( $this->c_line_width < ($this->c_findent + $this->c_lindent) ) {
				$tab_size = ($this->c_findent + $this->c_lindent) - $this->c_line_width;
			}
		}

		// --- IMAGE WRAP -----------------------------------------
		$wp_tot = 0; $c_line_wp = array();
		$cl_y = $this->y+$y_adj;//-$this->c_slines;
		if (isset($this->wrap_borders[$this->page]) && sizeof($this->wrap_borders[$this->page]) > 0) {
			$wp =& $this->wrap_borders[$this->page];

			reset($wp);
			while (list ($key, $val) = each ($wp)) {
				if ($cl_y >= ($val["Y1"]-$val["S"]) && $cl_y+($this->c_slines*0.2) <= ($val["Y2"]+$val["S"]) && $cl_y+($this->c_slines*1) < $this->h-$this->bMargin) {
					$wp_w = ($val["X2"] - $val["X1"]) + ($val["HS"]*2);
					//$w2 -= $wp_w;
					$wp_tot += $wp_w;
					$c_line_wp[] = array($val["X1"]-$val["HS"],$val["X2"]+$val["HS"]);
				}
			}
		}
		// --------------------------------------------------------

		$wmax=$w2*1000/$this->FontSize;
		//=====================================
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		$strlen = $this->GetStringWidth($txt);
		$strlen_f = $strlen*1000/$this->FontSize;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		//---- TABS -----
		if ($txt == "#%%#TAB#%%#") {
			$s = " ";$nb = 1;
			if (isset($this->c_par_tabs) && $this->c_par_tabs[0] != false ) {
				if (isset($this->c_par_tabs[0][$this->c_par_tab_count]) && $this->c_par_tabs[0][$this->c_par_tab_count]) {
					$tab_size = $this->c_par_tabs[0][$this->c_par_tab_count] - $this->c_line_width;
					//$tab_size = $this->c_par_tabs[0][$this->c_par_tab_count];
				}
			}
			$this->c_par_tab_count++;
		}
		$c_line = array();
		
		list($tmp_totsize,$tmp_totsize_n) = $this->_get_line_buff_size();
		$sep_l = 0;
		while($i<$nb) {
			$wp_flag=false;
			//Get next character
			$c=$s{$i};
			if($c==' ') {$sep=$i;$sep_l=$l;}
			if (sizeof($c_line_wp)>0) { // -- image wrap
				//$l+=$cw[$c];
				reset($c_line_wp);
				while (list ($key, $val) = each ($c_line_wp)) {

					$v1 = ($val[0]-$line_start);
					$v2 = ($val[1]-$line_start);
					$ccc_line = $tmp_totsize;
					$ccc_line_n = $tmp_totsize_n;
					if ($l>0) {
						$ll = $ccc_line_n+(($l+$cw[$c])*$this->FontSize/1000);
					}
					else {
						$ll = $ccc_line_n;
					}
					if ($ll>=$v1 && $ll<$v2) {
						if ($val[1]>$line_end && $val[1]<$this->w) {
							$wrap_out = $val[1]-$line_end;
							$val[1]=$line_end;
						}
						else {$wrap_out = false;}
						if ($line_start>$val[0] && $val[1]<$this->w) {
							$wp_size = $val[1] - $line_start;
						}
						else {
							$wp_size = $v2 - $v1;
						}
						$fin_wp_size = $wp_size+(($l-$sep_l)*$this->FontSize/1000);
						if ($sep == -1) {
							$tmp_str = "";
							$sep=-1;
							$l=0;
							$sep_l=0;
						}
						else {
							$tmp_str = substr($s,$j,$sep-$j);
							$this->c_string_buffer .= $tmp_str;
							$c_num = sizeof($this->c_string_buffer2);
							$this->c_string_buffer2[$c_num]["SPS"] = $cw[' '];
							$this->c_string_buffer2[$c_num]["TXT"] = $tmp_str;
							$this->c_string_buffer2[$c_num]["FONT"] = $this->c_font_pdf;
							$this->c_string_buffer2[$c_num]["FONT_SIZE"] = $this->FontSize;
							$this->c_string_buffer2[$c_num]["FONT_SIZE_PT"] = $this->FontSizePt;
							$this->c_string_buffer2[$c_num]["FONT_UP"] = $this->CurrentFont['up'];
							$this->c_string_buffer2[$c_num]["FONT_UT"] = $this->CurrentFont['ut'];
							if ($txt == "#%%#TAB#%%#") {
								$this->c_string_buffer2[$c_num]["SIZE"] = $tab_size;
								$this->c_line_width += $tab_size;
								$this->c_string_buffer2[$c_num]["TAB"] = 1;
							}
							else {
								$this->c_string_buffer2[$c_num]["SIZE"] = $this->GetStringWidth($tmp_str);
								$this->c_line_width += $this->GetStringWidth($tmp_str);
							}
							if($this->ColorFlag) { $this->c_string_buffer2[$c_num]["COLOR"] = $this->TextColor; }
							if($this->underline) { $this->c_string_buffer2[$c_num]["UNDER"] = "YES"; }
							if($this->c_subscr) { $this->c_string_buffer2[$c_num]["SUB"] = "YES"; }
							if($this->c_supscr) { $this->c_string_buffer2[$c_num]["SUP"] = "YES"; }
							
							$l=$sep_l;
							$sep_l=0;
							$i=$sep+1;
							$j = $i;
						}

						$tmp_str = "";
						$this->c_string_buffer .= $tmp_str;
						$c_num = sizeof($this->c_string_buffer2);
						$this->c_string_buffer2[$c_num]["SPS"] = $cw[' '];
						$this->c_string_buffer2[$c_num]["TXT"] = $tmp_str;
						$this->c_string_buffer2[$c_num]["FONT"] = $this->c_font_pdf;
						$this->c_string_buffer2[$c_num]["FONT_SIZE"] = $this->FontSize;
						$this->c_string_buffer2[$c_num]["FONT_SIZE_PT"] = $this->FontSizePt;
						$this->c_string_buffer2[$c_num]["FONT_UP"] = $this->CurrentFont['up'];
						$this->c_string_buffer2[$c_num]["FONT_UT"] = $this->CurrentFont['ut'];
							$this->c_string_buffer2[$c_num]["SIZE"] = $fin_wp_size;
							$this->c_line_width += $fin_wp_size;
							$this->c_string_buffer2[$c_num]["TAB"] = 1;
							$this->c_string_buffer2[$c_num]["WRAP"] = 1;
							$this->c_string_buffer2[$c_num]["WRAP_S"] = $val[1]-$val[0];
							if ($wrap_out) {
								$this->c_string_buffer2[$c_num]["WRAP_OUT"] = $wrap_out;
							}
							$this->c_string_buffer2[$c_num]["WRAP_X1"] = $val[0];
							$this->c_string_buffer2[$c_num]["WRAP_X2"] = $val[1];
							$this->c_string_buffer2[$c_num]["LINE_END"] = $line_end;
						if($this->ColorFlag) { $this->c_string_buffer2[$c_num]["COLOR"] = $this->TextColor; }

						$l+=$fin_wp_size*1000/$this->FontSize;
						break;
					}
				}
				$l+=$cw[$c];
			}
			else if ($txt == "#%%#TAB#%%#") { $l+=$tab_size*1000/$this->FontSize; } // TODO
			else {$l+=$cw[$c];}

			if($l>$wmax) {
				//Automatic line break
				if($sep==-1) {
					//=======
					list($tmp_totsize_t,$tmp_totsize_n_t) = $this->_get_line_buff_size();
					$res = $this->render_line();
					$line_start = 0;
					$y_adj = 0;
					list($tmp_totsize,$tmp_totsize_n) = $this->_get_line_buff_size();
					//=======
					if ($this->t_incell) {
						$w2 = $this->t_cell_w - ($this->t_padding*2) - $this->c_line_width - $this->c_rindent - $this->c_lindent;
						$line_start = $line_start + $this->c_lindent + $this->t_padding + $this->t_cell_before + $this->lMargin;
					}
					else {
						$w2 = $this->w - $this->rMargin - $this->lMargin - $this->c_line_width - $this->c_rindent - $this->c_lindent;
						$line_start = $line_start + $this->lMargin + $this->c_lindent;
					}
					if ($this->par_lines == 0 && $this->c_findent<>0) {
						$w2 = $w2 - ($this->c_findent);
						$line_start = $line_start + $this->c_findent;
					}
					// --- IMAGE WRAP -----------------------------------------
					$wp_tot = 0; $c_line_wp = array();
					$cl_y = $this->y+$y_adj;//+$this->c_slines;
					if (isset($this->wrap_borders[$this->page]) && sizeof($this->wrap_borders[$this->page]) > 0) {
						$wp =& $this->wrap_borders[$this->page];
						reset($wp);
						while (list ($key, $val) = each ($wp)) {

							if ($cl_y >= ($val["Y1"]-$val["S"]) && $cl_y+($this->c_slines*0.2) <= ($val["Y2"]+$val["S"]) && $cl_y+($this->c_slines*1) < $this->h-$this->bMargin) {
								$wp_w = ($val["X2"] - $val["X1"]) + ($val["HS"]*2);
								//$w2 -= $wp_w;
								$wp_tot += $wp_w;
								$c_line_wp[] = array($val["X1"]-$val["S"],$val["X2"]+$val["HS"]);
							}
						}
					}
					// --------------------------------------------------------

					$wmax=$w2*1000/$this->FontSize;
					$i++;
					if ($tmp_totsize_t<=$l) {
						$l = 0;
					}
					//$l = 0; // PROBLEM
					continue;
					/* MAY BE LATER - DO NOT DELETE!!!
										if($i==$j) {$i++;}
										//$this->myCell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
										$tmp_str = substr($s,$j,$i-$j);
										$this->c_string_buffer .= $tmp_str;
										$c_num = sizeof($this->c_string_buffer2);
										$this->c_string_buffer2[$c_num]["TXT"] = $tmp_str;
										$this->c_string_buffer2[$c_num]["FONT"] = $this->c_font_pdf;
										$this->c_string_buffer2[$c_num]["FONT_SIZE"] = $this->FontSize;
										$this->c_string_buffer2[$c_num]["SIZE"] = $this->GetStringWidth($tmp_str);
										if($this->ColorFlag) { $this->c_string_buffer2[$c_num]["COLOR"] = $this->TextColor; }
										if($this->underline) { $this->c_string_buffer2[$c_num]["UNDER"] = "YES"; }
										//$this->c_line_width += $this->GetStringWidth($tmp_str) *1000/$this->FontSize;
										$this->c_line_width += $this->GetStringWidth($tmp_str);
										$c_line[] = "1.".$tmp_str;
					*/
				}
				else if ($sep_l > 0) {
					$tmp_str = substr($s,$j,$sep-$j);
					$this->c_string_buffer .= $tmp_str;
					$c_num = sizeof($this->c_string_buffer2);
					$this->c_string_buffer2[$c_num]["SPS"] = $cw[' '];
					$this->c_string_buffer2[$c_num]["TXT"] = $tmp_str;
					$this->c_string_buffer2[$c_num]["FONT"] = $this->c_font_pdf;
					$this->c_string_buffer2[$c_num]["FONT_SIZE"] = $this->FontSize;
					$this->c_string_buffer2[$c_num]["FONT_SIZE_PT"] = $this->FontSizePt;
					$this->c_string_buffer2[$c_num]["FONT_UP"] = $this->CurrentFont['up'];
					$this->c_string_buffer2[$c_num]["FONT_UT"] = $this->CurrentFont['ut'];
					if ($txt == "#%%#TAB#%%#") {
						$this->c_string_buffer2[$c_num]["SIZE"] = $tab_size;
						$this->c_line_width += $tab_size;
						$this->c_string_buffer2[$c_num]["TAB"] = 1;
					}
					else {
						$this->c_string_buffer2[$c_num]["SIZE"] = $this->GetStringWidth($tmp_str);
						$this->c_line_width += $this->GetStringWidth($tmp_str);
					}
					if ($wp_flag) {
						$this->c_line_width += $wp_size;
						$this->c_string_buffer2[$c_num]["SIZE"] += $wp_size;
					}
					if($this->ColorFlag) { $this->c_string_buffer2[$c_num]["COLOR"] = $this->TextColor; }
					if($this->underline) { $this->c_string_buffer2[$c_num]["UNDER"] = "YES"; }
					if($this->c_subscr) { $this->c_string_buffer2[$c_num]["SUB"] = "YES"; }
					if($this->c_supscr) { $this->c_string_buffer2[$c_num]["SUP"] = "YES"; }
					$c_line[] = "2.".$tmp_str;
					$i=$sep+1;
				}
				$line_start = 0;
				if ($this->t_incell) {
					$w = $this->t_cell_w - ($this->t_padding*2) - $this->c_rindent - $this->c_lindent;
					$line_start = $line_start + $this->c_lindent + $this->t_padding + $this->t_cell_before + $this->lMargin;
				}
				else {
					$w=$this->w-$this->rMargin-$this->lMargin - $this->c_rindent - $this->c_lindent;
					$line_start = $line_start + $this->lMargin + $this->c_lindent;
				}
				if ($this->par_lines == 0 && $this->c_findent<>0) {
					//$w = $w - ($this->c_findent);
				}
				// --- IMAGE WRAP -----------------------------------------
				if ($this->par_lines > 0) {
					
					$wp_tot = 0; $c_line_wp = array();
					$cl_y = $this->y + ($this->c_slines*1.3)+$y_adj;
					if (isset($this->wrap_borders[$this->page]) && sizeof($this->wrap_borders[$this->page]) > 0) {
						$wp =& $this->wrap_borders[$this->page];
						reset($wp);
						while (list ($key, $val) = each ($wp)) {

						if ($cl_y >= ($val["Y1"]-$val["S"]) && $cl_y <= ($val["Y2"]+$val["S"]) && $cl_y < $this->h-$this->bMargin) {
								$wp_w = ($val["X2"] - $val["X1"]) + ($val["HS"]*2);
								//$w -= $wp_w;
								$wp_tot += $wp_w;
								$c_line_wp[] = array($val["X1"]-$val["HS"],$val["X2"]+$val["HS"]);
							}
						}
					}

				}
				// --------------------------------------------------------

				$wmax=$w*1000/$this->FontSize;
				$j = $i;
				$sep=-1;
				$sep_l=0;
				$l=0;
				//=======
				$this->render_line();
				list($tmp_totsize,$tmp_totsize_n) = $this->_get_line_buff_size();
				$y_adj = 0;
				//=======
			}
			else {
				$i++;
			}
		}

		if($i!=$j) {
			$tmp_str = substr($s,$j);
			$this->c_string_buffer .= $tmp_str;
			$c_num = sizeof($this->c_string_buffer2);
			$this->c_string_buffer2[$c_num]["SPS"] = $cw[' '];
			$this->c_string_buffer2[$c_num]["TXT"] = $tmp_str;
			$this->c_string_buffer2[$c_num]["FONT"] = $this->c_font_pdf;
			$this->c_string_buffer2[$c_num]["FONT_SIZE"] = $this->FontSize;
			$this->c_string_buffer2[$c_num]["FONT_SIZE_PT"] = $this->FontSizePt;
			$this->c_string_buffer2[$c_num]["FONT_UP"] = $this->CurrentFont['up'];
			$this->c_string_buffer2[$c_num]["FONT_UT"] = $this->CurrentFont['ut'];
			if ($txt == "#%%#TAB#%%#") {
				$this->c_string_buffer2[$c_num]["SIZE"] = $tab_size;
				$this->c_line_width += $tab_size;
				$this->c_string_buffer2[$c_num]["TAB"] = 1;
			}
			else {
				$this->c_string_buffer2[$c_num]["SIZE"] = $this->GetStringWidth($tmp_str);
				$this->c_line_width += $this->GetStringWidth($tmp_str);
			}
			if ($wp_flag) {
				$this->c_line_width += $wp_size;
				$this->c_string_buffer2[$c_num]["SIZE"] += $wp_size;
			}
			if($this->ColorFlag) { $this->c_string_buffer2[$c_num]["COLOR"] = $this->TextColor; }
			if($this->underline) { $this->c_string_buffer2[$c_num]["UNDER"] = "YES"; }
			if($this->c_subscr) { $this->c_string_buffer2[$c_num]["SUB"] = "YES"; }
			if($this->c_supscr) { $this->c_string_buffer2[$c_num]["SUP"] = "YES"; }

			$c_line[] = "3.".$tmp_str;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function render_line($last = false, $trow = false) {
		//if ($this->c_string_buffer == "" /* || trim($this->c_string_buffer) == "" */) {
		if (sizeof($this->c_string_buffer2) == 0 /* || trim($this->c_string_buffer) == "" */) {
			return false;
		}
		$this->par_lines++;
		// creating pdf line
		$tmp_string = "";
		$tmp_totsize = 0;
		$tmp_wrapsize_a = 0;
		$tmp_wrapsize_f = 0;
		$tmp_wrap_out = 0;
		$chunks = sizeof($this->c_string_buffer2);
		for ($i=0;$i<$chunks;$i++) {
			$val =& $this->c_string_buffer2[$i];
			if ($i==0 || isset($this->c_string_buffer2[$i-1]["TAB"])) {
				$bef = strlen($val["TXT"]);
				$val["TXT"] = ltrim($val["TXT"]);
				$aft = strlen($val["TXT"]);
				if ($aft < $bef) {
					$sps = $val["SPS"] * ($bef-$aft);
					$val["SIZE"] = $val["SIZE"] - ($sps*$val["FONT_SIZE"]/1000);
				}
			}
			if ($i==($chunks-1) || isset($this->c_string_buffer2[$i+1]["TAB"])) {
				$bef = strlen($val["TXT"]);
				$val["TXT"] = rtrim($val["TXT"]);
				$aft = strlen($val["TXT"]);
				if ($aft < $bef) {
					$sps = $val["SPS"] * ($bef-$aft);
					$val["SIZE"] = $val["SIZE"] - ($sps*$val["FONT_SIZE"]/1000);
				}
			}
			$tmp_string .= $val["TXT"];
			$tmp_totsize += $val["SIZE"];
			if (isset($val["WRAP_S"])) {
//				$tmp_wrapsize_f += $val["SIZE"];
//				$tmp_wrapsize_a += $val["WRAP_S"];
			}
			if (isset($val["WRAP_OUT"])) {
				$tmp_wrap_out += $val["WRAP_OUT"];
				$tmp_wrapsize_f += $val["SIZE"];
				$tmp_wrapsize_a += $val["WRAP_S"];
			}
		}
		// --- PLACING THE LINE TO PDF CODE ---------------------------
		$adj = 0;
		$ws_adj = false;

		if ($this->t_incell) {
			$available_width = $this->t_cell_w - ($this->t_padding*2) - $this->c_rindent - $this->c_lindent;
		}
		else {
			$available_width = $this->w - $this->rMargin - $this->lMargin - $this->c_rindent - $this->c_lindent;
		}
		if ($this->t_incell && !$this->in_par) {
			if (isset($this->t_cell_align) && $this->t_cell_align) {
				$this->line_align = $this->t_cell_align;
			}
		}
		else {
			$this->line_align = $this->c_align;
		}

		if ($this->par_lines == 1 && $this->c_findent<>0) {
			$available_width = $available_width - ($this->c_findent);
		}

		$available_width = $available_width - $tmp_wrapsize_a;
		$tmp_totsize = $tmp_totsize - $tmp_wrapsize_f;

		if ($this->line_align == "C") {
			$adj = ($available_width - $tmp_totsize) / 2;
		}
		else if ($this->line_align == "R") {
			$adj = $available_width - $tmp_totsize;
		}
		else if ($this->line_align == "FJ" && !$last) {
			if (substr_count($tmp_string,' ') == 0) {
				$ws_adj = ($available_width-$tmp_totsize);
			}
			else {
				$ws_adj = ($available_width-$tmp_totsize)/substr_count($tmp_string,' ');
			}
		}
		$h = $this->c_slines;
		$this->x = $this->lMargin + $adj + $this->c_lindent;

		if ($this->par_lines == 1 && $this->c_findent<>0) {
			$this->x = $this->x + ($this->c_findent);
		}
		if ($this->t_incell) {
			$this->x += $this->t_cell_before+$this->t_padding;
		}
		$w = $tmp_totsize;
		$k=$this->k;
		if ($this->par_lines == 1) {
			$this->y += $this->c_sbefore;
		}
		if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak()) {
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0) {
				$this->ws=0;
				$this->_out('0 Tw');
			}
			if (!isset($this->pages[$this->page+1])) {
				$this->AddPage($this->CurOrientation);
			}
			else {
				$this->page++;
				$this->y=$this->tMargin;
			}
			$this->x=$x;
			if($ws>0) {
				$this->ws=$ws;
				$this->_out(sprintf('%.3f Tw',$ws*$k));
			}
		}

		$s = ""; $sps_count = 0;
		for ($i=0;$i<$chunks;$i++) {
			$val =& $this->c_string_buffer2[$i];
			$tmp_string = $val["TXT"];
			if ($i==0) {
				$tmp_string = ltrim($tmp_string);
			}
			if ($i==$chunks-1) {
				$tmp_string = rtrim($tmp_string);
			}
			$tmp_size = $val["SIZE"];
			$tmp_fsize = $val["FONT_SIZE"];
			if (isset($val["COLOR"])) {
				$tmp_color = $val["COLOR"];
			}
			else {$tmp_color = "0.00 g";}
			$tmp_font = $val["FONT"];
			if ($ws_adj && $i>0) {
				$dx = $ws_adj*$sps_count;
			}
			else {$dx=0;}
			//--------------------------------------
			$s .= $tmp_font."\n";
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$tmp_string)));
			$sps_count += substr_count($txt,' ');
			if (isset($val["SUB"])) {
				$s .= "-".($val["FONT_SIZE"])." Ts"."\n";
			}
			else if (isset($val["SUP"])) {
				$s .= "".($val["FONT_SIZE"])." Ts"."\n";
			}
			if($tmp_color) { $s.='q '.$tmp_color.' '; }

			if (isset($this->c_string_buffer2[$i-1]) && isset($this->c_string_buffer2[$i-1]["WRAP"]) &&
						$this->line_align != "R" && $this->line_align != "C"
				) { // WRAP
				$s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->c_string_buffer2[$i-1]["WRAP_X2"])*$k,($this->h-($this->y+.5*$h+.3*$tmp_fsize))*$k,$txt);
				if(isset($val["UNDER"])) {
					$s.=' '.$this->_dounderline_exp($this->c_string_buffer2[$i-1]["WRAP_X2"],$this->y+.5*$h+.3*$tmp_fsize,$tmp_size+($ws_adj*substr_count($txt,' ')),$val["FONT_UP"],$val["FONT_UT"],$tmp_fsize,$val["FONT_SIZE_PT"]);
				}
			}
			else {
				$s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$tmp_fsize))*$k,$txt);
				if(isset($val["UNDER"])) {
					$s.=' '.$this->_dounderline_exp($this->x+$dx,$this->y+.5*$h+.3*$tmp_fsize,$tmp_size+($ws_adj*substr_count($txt,' ')),$val["FONT_UP"],$val["FONT_UT"],$tmp_fsize,$val["FONT_SIZE_PT"]);
				}
			}
			if($tmp_color){$s.=' Q';}
			if (isset($val["SUB"]) || isset($val["SUP"])) {
				$s .= "\n"."0 Ts";
			}
			$s .= "\n";
			$this->x += $tmp_size;
			//--------------------------------------
		}
		$this->lasth=$this->c_slines;
		$this->y+=$this->c_slines;
		// ------------------------------------------------------------
		if ($ws_adj) {
			$this->_out(sprintf('%.3f Tw',$ws_adj*$this->k));
		}
		if($s) {$this->_out($s);}
		if ($ws_adj) {
			$this->_out('0 Tw');
		}
		$this->c_par_tab_count = 0;
		$this->c_string_buffer = "";
		$this->c_string_buffer2 = array();
		$this->c_line_width = 0;
		if ($last===true) {
			$this->par_lines = 0;
			$this->y+=$this->c_safter;
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _draw_table_borders($t) {
		$k =& $this->k;
		$out = array();
		$out_f = array();
		$tx1 = false;
		$tx2 = false;
		$ty1 = false;
		$ty2 = false;
		$tp1 = false;
		$tp2 = false;
		$fst_pg = false;
		$lst_pg = false;

		$row_y = array();

		$c_page = $this->page;
		$c_fill_color = $this->FillColor;
		// ==== FIRST PASS =====================
		reset($t);$row_prew_y2 = false;$row_prew_p = false;
		while (list ($key1, $row) = each ($t)) {
			reset($row); $row_y2 = false; $row_pg2 = false;
			while (list ($key2, $cell) = each ($row)) {
				if ($fst_pg===false) {$fst_pg = $cell["P1"];}
				if ($lst_pg===false || $lst_pg < $cell["P2"]) {$lst_pg = $cell["P2"]; $ty2=false;}
				if ($tx1===false) {$tx1 = $cell["TD_X1"];}
				if ($ty1===false) {$ty1 = $cell["TD_Y1"]-$cell["PADDING"];}
				if ($tx2===false || $tx2 < $cell["TD_X2"]) {$tx2 = $cell["TD_X2"];}
				if ($ty2===false || ($ty2 < $cell["TD_Y2"] && $lst_pg == $cell["P2"])) {$ty2 = $cell["TD_Y2"]+$cell["PADDING"];}
				if (isset($cell["ROWSPAN"]) && $cell["ROWSPAN"] > 0) {
					if (!isset($row_y[$key1+$cell["ROWSPAN"]]["P"]) || $row_y[$key1+$cell["ROWSPAN"]]["P"] < $cell["P2"]) {
						$row_y[$key1+$cell["ROWSPAN"]]["P"] = $cell["P2"];
						$row_y[$key1+$cell["ROWSPAN"]]["Y2"] = $cell["TD_Y2"];
					}
					if (!isset($row_y[$key1+$cell["ROWSPAN"]]["Y2"]) || ($row_y[$key1+$cell["ROWSPAN"]]["Y2"] < $cell["TD_Y2"] && $row_y[$key1+$cell["ROWSPAN"]]["P"] == $cell["P2"])) {
						$row_y[$key1+$cell["ROWSPAN"]]["Y2"] = $cell["TD_Y2"];
					}
				}
				else {
					if (!isset($row_y[$key1]["P"]) || $row_y[$key1]["P"] < $cell["P2"]) {
						$row_y[$key1]["P"] = $cell["P2"];
						$row_y[$key1]["Y2"] = $cell["TD_Y2"];
					}
					if (!isset($row_y[$key1]["Y2"]) || ($row_y[$key1]["Y2"] < $cell["TD_Y2"] && $row_y[$key1]["P"] == $cell["P2"])) {
						$row_y[$key1]["Y2"] = $cell["TD_Y2"];
					}
				}
				//----------------------------------------------------
			}
		}
		// ==== SECOND PASS ====================
		reset($t);
		while (list ($key1, $row) = each ($t)) {
			reset($row);
			while (list ($key2, $cell) = each ($row)) {
				//----------------------------------------------------
				$x1 = $cell["TD_X1"];
				$y1 = $cell["TD_Y1"]-$cell["PADDING"];
				$x2 = $cell["TD_X2"];
				$p1 = $cell["P1"];
				if (isset($cell["ROWSPAN"]) && $cell["ROWSPAN"] > 0) {
					$y2 = $row_y[$key1+$cell["ROWSPAN"]]["Y2"];
					$p2 = $row_y[$key1+$cell["ROWSPAN"]]["P"];
				}
				else {
					$y2 = $row_y[$key1]["Y2"];
					$p2 = $row_y[$key1]["P"];
				}
				if (!isset($out[$p1])) {$out[$p1]="";}
				if (!isset($out[$p2])) {$out[$p2]="";}
				if (!isset($out_f[$p1])) {$out_f[$p1]="";}
				if (!isset($out_f[$p2])) {$out_f[$p2]="";}
				//-------------------------------------------------------
				if (isset($cell["BORDER"])) {
					$b =& $cell["BORDER"];
				}
				else { $b = false; }
				// --- cell top border
				if ($b && (preg_match("/T/",$b) || $b == 1)) {
					$out[$p1] .= "\n".$this->_out_line($x1,$y1,$x2,$y1);
				}
				// --- cell bottom border
				if ($b && (preg_match("/B/",$b) || $b == 1)) {
					$out[$p2] .= "\n".$this->_out_line($x1,$y2,$x2,$y2);
				}
				// --- cell side borders

				if ($p1==$p2) {
					// --- left border
					if ($b && (preg_match("/L/",$b) || $b == 1)) {
						$out[$p1] .= "\n".$this->_out_line($x1,$y1,$x1,$y2);
					}
					// --- right border
					if ($b && (preg_match("/R/",$b) || $b == 1)) {
						$out[$p1] .= "\n".$this->_out_line($x2,$y1,$x2,$y2);
					}
					//------- FILL --------------------
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p1] .= "\n".sprintf('%.3f %.3f %.3f rg',$cell["BGCOLOR"][0]/255,$cell["BGCOLOR"][1]/255,$cell["BGCOLOR"][2]/255);
					}
					else {$out_f[$p1] .= "\n"."1 g";}
					$out_f[$p1] .= "\n".sprintf('%.3f %.3f %.3f %.3f re f',($this->lMargin+$x1)*$k,($this->h-$y2)*$k,($x2-$x1)*$k,($y2-$y1)*$k);
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p1] .= "\n".$c_fill_color;
					}
					//---------------------------------
				}
				else {
					// --- left border
					if ($b && (preg_match("/L/",$b) || $b == 1) && $this->h-$this->bMargin > $y1) {
						$out[$p1] .= "\n".$this->_out_line($x1,$y1,$x1,$this->h-$this->bMargin);
					}
					// --- right border
					if ($b && (preg_match("/R/",$b) || $b == 1) && $this->h-$this->bMargin > $y1) {
						$out[$p1] .= "\n".$this->_out_line($x2,$y1,$x2,$this->h-$this->bMargin);
					}
					//------- FILL --------------------
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p1] .= "\n".sprintf('%.3f %.3f %.3f rg',$cell["BGCOLOR"][0]/255,$cell["BGCOLOR"][1]/255,$cell["BGCOLOR"][2]/255);
					}
					else {$out_f[$p1] .= "\n"."1 g";}
					$out_f[$p1] .= "\n".sprintf('%.3f %.3f %.3f %.3f re f',($this->lMargin+$x1)*$k,($this->h-($this->h-$this->bMargin))*$k,($x2-$x1)*$k,(($this->h-$this->bMargin)-$y1)*$k);
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p1] .= "\n".$c_fill_color;
					}
					//=======================================
					// --- left border
					if ($b && (preg_match("/L/",$b) || $b == 1)) {
						$out[$p2] .= "\n".$this->_out_line($x1,$this->tMargin,$x1,$y2);
					}
					// --- right border
					if ($b && (preg_match("/R/",$b) || $b == 1)) {
						$out[$p2] .= "\n".$this->_out_line($x2,$this->tMargin,$x2,$y2);
					}
					//------- FILL --------------------
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p2] .= "\n".sprintf('%.3f %.3f %.3f rg',$cell["BGCOLOR"][0]/255,$cell["BGCOLOR"][1]/255,$cell["BGCOLOR"][2]/255);
					}
					else {$out_f[$p2] .= "\n"."1 g";}
					$out_f[$p2] .= "\n".sprintf('%.3f %.3f %.3f %.3f re f',($this->lMargin+$x1)*$k,($this->h-$y2)*$k,($x2-$x1)*$k,($y2-$this->tMargin)*$k);
					if (isset($cell["BGCOLOR"]) && is_array($cell["BGCOLOR"])) {
						$out_f[$p2] .= "\n".$c_fill_color;
					}
				}
				//----------------------------------------------------
			}
		}
		reset($out);
		while (list ($key, $val) = each ($out)) {

			if ($this->flg_header) {
				$this->iHeader = "\n".$val."\n".$this->iHeader;
				return;
			}
			else if ($this->flg_footer) {
				$this->iFooter = "\n".$val."\n".$this->iFooter;
				return;
			}
			else {
				$this->pages[$key] = preg_replace("/%PAGE_START/","%PAGE_START \n".$val,$this->pages[$key]);
			}
		}
		reset($out_f);
		while (list ($key, $val) = each ($out_f)) {
			if ($this->flg_header) {
				$this->iHeader .= "\n".$val."\n".$this->iHeader;
				return;
			}
			else if ($this->flg_footer) {
				$this->iFooter .= "\n".$val."\n".$this->iFooter;
				return;
			}
			else {
				$this->pages[$key] = preg_replace("/%PAGE_START/","%PAGE_START \n".$val,$this->pages[$key]);
			}
		}
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function _out_line($x1,$y1,$x2,$y2) {
		$k =& $this->k;
		return sprintf('%.3f %.3f m %.3f %.3f l S',($this->lMargin+$x1)*$k,($this->h-$y1)*$k,($this->lMargin+$x2)*$k,($this->h-$y2)*$k);
	} // end of function
// ------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------
	function myWrite($h,$txt,$link='') {
		$this->myWrite2($h,$txt,$link);
		return;
	} // end of function
// ------------------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------------------
	function Image($file,$img,$x,$y,$w=0,$h=0,$type='',$link='') {
		//Put an image on the page
		if(!isset($this->images[$file]))
		{
			//First use of image, get info
			if($type=='')
			{
				$pos=strrpos($file,'.');
				if(!$pos)
					$this->Error('Image file has no extension and no type was specified: '.$file);
				$type=substr($file,$pos+1);
			}
			$type=strtolower($type);
			$mqr=get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);
			if($type=='jpg' || $type=='jpeg')
				$info=$this->_parsejpg($file);
			elseif($type=='png')
				$info=$this->_parsepng($file);
			else
			{
				//Allow for additional formats
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported image type: '.$type);
				$info=$this->$mtd($file);
			}
			set_magic_quotes_runtime($mqr);
			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		else
			$info=$this->images[$file];
		//Automatic width and height calculation if needed
		$w = $img["WIDTH"];
		$h = $img["HEIGHT"];
		if($w==0 && $h==0)
		{
			//Put image at 72 dpi
			$w=$info['w']/$this->k;
			$h=$info['h']/$this->k;
		}
		if($w==0)
			$w=$h*$info['w']/$info['h'];
		if($h==0)
			$h=$w*$info['h']/$info['w'];
		//---------------------------------------------------
		//--- ALIGN
		switch ($img["ANCHOR"]) {
			case "PARA": 
						$y0 = $this->par_start_y;
						$x0 = $this->lMargin;
						break;
			case "PAGE":
						$y0 = 0;
						$x0 = 0;
						break;
			case "MARGIN":
						$y0 = $this->tMargin;
						$x0 = $this->lMargin;
						break;
			case "INCELL":
						if (isset($img["TD_Y1"]) && $this->t_incell) {
							$y0 = $img["TD_Y1"];
							$x0 = $this->lMargin + $this->t_cell_before + $this->t_padding + $this->c_lindent;
						}
						else {
							$y0 = $this->par_start_y;
							$x0 = $this->lMargin;
						}
						break;
		}

		switch ($img["ALIGN"]) {
			case "RIGHT":
						if ($img["ANCHOR"] == "PAGE") {
							$x0 = $this->w - $x0 - $w;
						}
						else if ($img["ANCHOR"] == "INCELL" && isset($img["TD_Y1"]) && $this->t_incell) {
							$x0 = ($x0 + $this->t_cell_w - $w) - ($this->t_padding*2);
						}
						else {
							$x0 = $this->w - $this->rMargin - $w;
						}
						break;
			case "LEFT":
						break;
			case "CENTER":
						if ($img["ANCHOR"] == "PAGE") {
							$x0 = ($this->w - $x0 - $w)/2;
						}
						else if ($img["ANCHOR"] == "INCELL" && isset($img["TD_Y1"]) && $this->t_incell) {
							$x0 = $x0 - $this->t_padding +($this->t_cell_w - $w)/2;
						}
						else {
							$x0 = ($this->w - $w)/2;
						}
						break;
		}
		$x = $x0 + $img["LEFT"];
		$y = $y0 + $img["TOP"];

		if ($img["WRAP"] == "UPDOWN") {
			if ($img["ANCHOR"] == "INCELL" && isset($img["TD_Y1"]) && $this->t_incell) {
				$w_x1 = $this->lMargin + $this->t_cell_before + $this->c_lindent;
				$w_x2 = $w_x1 + $this->t_cell_w;
			}
			else {
				$w_x1 = 0;
				$w_x2 = $this->w;
			}
		}
		else {
			$w_x1 = $x;
			$w_x2 = $x + $w;
		}
		$w_y1 = $y;
		$w_y2 = $y + $h;
		$s = "";

		if ($img["WRAP"] != "NO") {
			if (!isset($this->wrap_borders[$this->page])) {
				$this->wrap_borders[$this->page] = array();
			}
			$sw = sizeof($this->wrap_borders[$this->page]);
			$this->wrap_borders[$this->page][$sw]["X1"] = $w_x1;
			$this->wrap_borders[$this->page][$sw]["Y1"] = $w_y1;
			$this->wrap_borders[$this->page][$sw]["X2"] = $w_x2;
			$this->wrap_borders[$this->page][$sw]["Y2"] = $w_y2;
			$this->wrap_borders[$this->page][$sw]["S"]  = $img["SPACE"];
			if ($img["WRAP"] == "UPDOWN") {
				$this->wrap_borders[$this->page][$sw]["HS"]  = 0;
			}
			else {
				$this->wrap_borders[$this->page][$sw]["HS"]  = $img["SPACE"];
			}
			
			$k =& $this->k;
			if (isset($img["BORDER"]) && $img["BORDER"] > 0) {
				$s .= "\n".sprintf('%.3f %.3f %.3f %.3f re S',
										$w_x1*$k,
										($this->h-$w_y2)*$k,
										($w_x2-$w_x1)*$k,
										($w_y2-$w_y1)*$k);
			}
/* for debug - draw wrap borders
			$s .= "\n".sprintf('%.3f %.3f %.3f %.3f re S',
									($w_x1-$this->wrap_borders[$this->page][$sw]["HS"])*$k,
									($this->h-($w_y2+$this->wrap_borders[$this->page][$sw]["S"]))*$k,
									($w_x2-$w_x1+($this->wrap_borders[$this->page][$sw]["HS"]*2))*$k,
									(($w_y2-$w_y1+($this->wrap_borders[$this->page][$sw]["S"]*2)))*$k);
			$s .= "\n".sprintf('%.3f %.3f m %.3f %.3f l S',
									($w_x1-$this->wrap_borders[$this->page][$sw]["HS"])*$k,
									($this->h-($w_y1-$this->wrap_borders[$this->page][$sw]["S"]))*$k,
									($w_x2+$this->wrap_borders[$this->page][$sw]["HS"])*$k,
									($this->h-($w_y2+$this->wrap_borders[$this->page][$sw]["S"]))*$k);

*/
		}
		$s .= "\n".sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']);
		

		if ($this->flg_header) {
			$this->iHeader = "\n".$s."\n".$this->iHeader;
			return;
		}
		else if ($this->flg_footer) {
			$this->iFooter = "\n".$s."\n".$this->iFooter;
			return;
		}
		else {
			$this->pages[$this->page] = preg_replace("/%PAGE_STR_TXT/","%PAGE_STR_TXT \n".$s,$this->pages[$this->page]);
		}
	

		if($link)
			$this->Link($x,$y,$w,$h,$link);
	} // end of function
// ------------------------------------------------------------------------------------------------

} // END OF CLASS
// ------------------------------------------------------------------------------------------------




?>
