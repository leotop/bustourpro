<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('START_PATH', $_SERVER['DOCUMENT_ROOT']); // стартовая папка для поиска
define('LOG', $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.xscan/file_list.txt'); // лог файл 
define('START_TIME', time()); // засекаем время старта

$APPLICATION->SetTitle(GetMessage("BITRIX_XSCAN_SEARCH"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$action = $_REQUEST['action'];
if (($file = $_REQUEST['file']) && check_bitrix_sessid())
{
	$str = file_get_contents(LOG);
	$f = START_PATH.$file;
	if (!file_exists($f))
		ShowMsg(GetMessage("BITRIX_XSCAN_FILE_NOT_FOUND").htmlspecialchars($f), 'red');
	if (!CheckFile($f))
		ShowMsg(GetMessage("BITRIX_XSCAN_FAYL_NE_VYGLADIT_POD").htmlspecialchars($file), 'red');
	else
	{
		if ($action == 'prison')
		{
			$new_f = preg_replace('#\.php$#', '.ph_', $f);
			if (rename($f, $new_f))
				ShowMsg(GetMessage("BITRIX_XSCAN_RENAMED").htmlspecialchars($new_f));
			else
				ShowMsg(GetMessage("BITRIX_XSCAN_ERR_RENAME").htmlspecialchars($f), 'red');
		}
		elseif ($action == 'showfile')
		{
			echo '<h2>'.GetMessage("BITRIX_XSCAN_FAYL").htmlspecialchars($file).'</h2>';
			$code = intval($_REQUEST['code']);

			$str = '';
			$read = false;
			$rs = fopen(__FILE__, 'rb');
			while(false !== $l = fgets($rs))
			{
				if ($read && preg_match('/# CODE /', $l))
					break;
				if (preg_match('/# CODE '.$code.'/', $l))
					$read = true;
				if ($read)
					$str .= $l;
			}
			fclose($rs);

			if ($str)
			{
				echo '<div>'.GetMessage("BITRIX_XSCAN_POCEMU_VYBRAN_ETOT_F").'</div>';
				highlight_string("<"."?\n".$str."\n");
			}

			if ($LAST_REG)
			{
				echo '<div>'.GetMessage("BITRIX_XSCAN_PODOZRITELQNYY_KOD").'</div>';
				echo '<div style="border:1px solid #CCC;padding:10px">'.nl2br(htmlspecialcharsbx($LAST_REG)).'</div>';
			}

			$str = file_get_contents($f);
			highlight_string($str);
		}
	}
	die();
}

if ($_REQUEST['go'])
{
	if ($_REQUEST['break_point'])
		define('SKIP_PATH',htmlspecialchars($_REQUEST['break_point'])); // промежуточный путь
	elseif (file_exists(LOG))
		unlink(LOG);

	Search(START_PATH);
	if (file_exists(LOG))	
		ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED_FOUND"), 'red');
	if (defined('BREAK_POINT'))
	{
			?><form method=post id=postform action=?>
			<input type=hidden name=go value=Y>
			<input type=hidden name=break_point value="<?=htmlspecialchars(BREAK_POINT)?>">
			</form>
			<?
			ShowMsg('<b>'.GetMessage("BITRIX_XSCAN_IN_PROGRESS").'...</b><br>
			'.GetMessage("BITRIX_XSCAN_CURRENT_FILE").': <i>'.htmlspecialchars(str_replace(START_PATH,'',BREAK_POINT)).'</i>');
			?>
			<script>window.setTimeout("document.getElementById('postform').submit()",500);</script><? // таймаут чтобы браузер показал текст
			die();
	}
	else
	{
		if (!file_exists(LOG))
			ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED"));
	}
}
?><form method=post action=?>
	<input type=submit name=go value="<?=GetMessage("BITRIX_XSCAN_START_SCAN")?>">
</form><?

if (file_exists(LOG))
{
	echo '<table width=80% border=1 style="border-collapse:collapse;border-color:#CCC">';
	echo '<tr>
		<th>'.GetMessage("BITRIX_XSCAN_NAME").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_TYPE").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_SIZE").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_M_DATE").'</th>
		<th></th>
	</tr>';

	$ar = file(LOG);
	foreach($ar as $line)
	{
		list($f, $type) = explode("\t", $line);
		{
			$code = preg_match('#\[([0-9]+)\]#', $type, $regs) ? $regs[1] : 0;
			$fu = urlencode(trim($f));
			$bInPrison = trim($type) != 'htaccess';
			echo '<tr>
				<td><a href="?action=showfile&file='.$fu.'&code='.$code.'&'.bitrix_sessid_get().'" title="'.GetMessage("BITRIX_XSCAN_SRC").'" target=_blank>'.htmlspecialchars($f).'</a></td>
				<td>'.htmlspecialchars($type).'</td>
				<td>'.HumanSize(filesize($_SERVER['DOCUMENT_ROOT'].$f)).'</td>
				<td>'.date('d.m.Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].$f)).'</td>
				<td>'.($bInPrison ? '<a href="?action=prison&file='.$fu.'&'.bitrix_sessid_get().'" onclick="if(!confirm(\''.GetMessage("BITRIX_XSCAN_WARN").'\'))return false;" title="'.GetMessage("BITRIX_XSCAN_QUESTION").'">'.GetMessage("BITRIX_XSCAN_KARANTIN") : '').'</td>
			</tr>';
		}
	}
	echo '</table>';
}

function Search($path)
{
	if (time() - START_TIME > 10)
	{
		if (!defined('BREAK_POINT'))
			define('BREAK_POINT', $path);
		return;
	}

	if (defined('SKIP_PATH') && !defined('FOUND')) // проверим, годится ли текущий путь
	{
		if (0 !== bin_strpos(SKIP_PATH, dirname($path))) // отбрасываем имя или идём ниже 
			return;

		if (SKIP_PATH==$path) // путь найден, продолжаем искать текст
			define('FOUND',true);
	}

	if (is_dir($path)) // dir
	{
		$p = realpath($path);
		if (strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache') === 0
		|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/managed_cache') === 0
		|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/stack_cahe') === 0
//		|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix') === 0
		)
			return;

		if (is_link($path))
		{
			if (strpos($p, $_SERVER['DOCUMENT_ROOT']) !== false) // если симлинк ведет на папку внутри структуры сайта
				return true;
		}

		$dir = opendir($path);
		while($item = readdir($dir))
		{
			if ($item == '.' || $item == '..')
				continue;

			Search($path.'/'.$item);
		}
		closedir($dir);
	}
	else // file
	{
		if (!defined('SKIP_PATH') || defined('FOUND'))
			if ($res = CheckFile($path))
				Mark($path, $res);
	}
}

function CheckFile($f)
{
	$spaces = "[ \r\t\n]*";
	global $LAST_REG;

	static $me;
	if (!$me)
		$me = realpath(__FILE__);
	if (realpath($f) == $me)
		return false;
	# CODE 100
	if (basename($f) == '.htaccess')
	{
		$str = file_get_contents($f);
		$res = preg_match('#<(\?|script)#i',$str,$regs);
		$LAST_REG = $regs[0];
		return $res ? '[100] htaccess' : false;
	}

	if (!preg_match('#\.php$#',$f,$regs))
		return false;

	# CODE 200
	if (false === $str = file_get_contents($f))
		return '[200] read error';

	# CODE 300
	if (preg_match('#[^a-z](eval|assert|call_user_func|call_user_func_array|create_function|ob_start)'.$spaces.'\([^\)]*\$_(POST|GET|REQUEST|COOKIE|SERVER)#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!LooksLike($f, array('/bitrix/components/bitrix/webdav.section.list/action.php', '/bitrix/modules/webdav/install/components/bitrix/webdav.section.list/action.php', '/bitrix/modules/catalog/options.php', '/bitrix/modules/main/admin/php_command_line.php', '/bitrix/modules/iblock/classes/mssql/cml2.php', '/bitrix/modules/iblock/classes/oracle/cml2.php', '/bitrix/modules/xdimport/admin/lf_scheme_getentity.php','/bitrix/modules/iblock/classes/mysql/cml2.php')) && !preg_match('#CTaskAssert::assert\(#', $str))
			return '[300] eval';
	}

	# CODE 400
	if (preg_match('#\$(USER|GLOBALS..USER..)->Authorize'.$spaces.'\([0-9]+\)#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!LooksLike($f, array('/bitrix/modules/bxtest/tests/security/filter/.base.php', '/bitrix/modules/bxtest/tests/tasks/classes/ctaskitem/.01_longtime_random_test.php', '/bitrix/modules/bxtest/tests/tasks/classes/ctaskitem/.bootstrap.php', '/bitrix/modules/main/install/install.php','/bitrix/modules/dav/classes/general/principal.php','/bitrix/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php','/bitrix/modules/controller/install/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php')))
			return '[400] bitrix auth';
	}

	# CODE 500
	if (preg_match('#[\'"]php://filter#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!LooksLike($f, array('/bitrix/modules/bxtest/tests/main/classes/cfile/02_make_file_array.php')))
			return '[500] php wrapper';
	}

	# CODE 600
	if (preg_match('#(include|require)(_once)?'.$spaces.'\([^\)]+\.([a-z0-9]+).'.$spaces.'\)#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if ($regs[3] != 'php')
			return '[600] strange include';
	}

	# CODE 610
	if (preg_match('#\$__+[^a-z_]#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		return '[610] strange vars';
	}

	# CODE 620
	if (preg_match('#\$['."_\x80-\xff".']+'.$spaces.'=#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		return '[620] binary vars';
	}

	# CODE 630
	if (preg_match('#[a-z0-9+=/]{255,}#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!preg_match('#data:image/[^;]+;base64,[a-z0-9+=/]{255,}#i', $str, $regs))
		{
			if (!preg_match('#\$ser_content = \'#',$str))
				return '[630] long line';
		}
	}

	# CODE 640
	if (preg_match('#exif_read_data\(#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!LooksLike($f, array('/bitrix/modules/main/classes/general/file.php')))
			return '[640] strange exif';
	}

	# CODE 650
	if (preg_match('#\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*'.$spaces.'\('.$spaces.'"(\\\\x([a-f0-9]{2}|[0-9]{3}))#i', $str, $regs))
	{
		$LAST_REG = $regs[0];
		return '[650] variable as function';
	}

	# CODE 700
	if (preg_match('#file_get_contents\(\$[^\)]+\);[^a-z]*file_put_contents#mi', $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!LooksLike($f, array('/bitrix/components/bitrix/extranet.group_create/component.php', '/bitrix/components/bitrix/webdav/templates/.default/bitrix/bizproc.document/webdav.bizproc.document/template.php')))
			return '[700] file from variable';
	}

	# CODE 710
	if (preg_match('#file_get_contents\([\'"]https?://#mi', $str, $regs))
	{
		$LAST_REG = $regs[0];
		return '[710] file from the Internet';
	}

	# CODE 640
	if (preg_match("#[\x01-\x08\x0b\x0c\x0f-\x1f]#", $str, $regs))
	{
		$LAST_REG = $regs[0];
		if (!preg_match('#\$ser_content = \'#',$str))
			return '[640] binary data';
	}

	# CODE 800
	if (preg_match('#preg_replace\(\$_#mi', $str, $regs))
	{
		$LAST_REG = $regs[0];
		return '[800] preg_replace pattern from variable';
	}

	# CODE END
	return false;
}

function LooksLike($f, $mask)
{
	$f = str_replace('\\','/',$f);
	if (is_array($mask))
	{
		foreach($mask as $m)
		{
			if (preg_match('#'.$m.'$#',$f))
				return true;
		}
	}
	return preg_match('#'.$mask.'$#',$f);
}

function bin_strpos($s, $a)
{
	if (function_exists('mb_orig_strpos'))
		return mb_orig_strpos($s, $a);
	return strpos($s, $a);
}

function Mark($f, $type)
{
	if (false === file_put_contents(LOG, str_replace(START_PATH,'',$f)."\t".$type."\n", 8))
	{
		ShowError('Write error: '.LOG);
		die();
	}
}

function ShowMsg($str, $color = 'green')
{
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => '',
		"DETAILS" => $str,
		"TYPE" => $color == 'green' ? "OK" : 'ERROR',
		"HTML" => true));
}

function HumanSize($s)
{
	$i = 0;
	$ar = array('b','kb','M','G');
	while($s > 1024)
	{
		$s /= 1024;
		$i++;
	}
	return round($s,1).' '.$ar[$i];
}

?>
