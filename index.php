<?php
$folder = './';
$title = ['タイトル', '第"%s"章　', 'counter(i,cjk-ideographic)'];
#$title = ['Title', 'Chapter "%s"　', 'counter(i,upper-roman)'];
$confirm = ['削除しますか？', '「%s」にアーカイブを保存しますか？'];
$aside = ['350px', '450px', '2em'];
$icon = ['➕', '📥', '❌'];
$delimiter = '.';
$disallow_symbols = ['"','#','$','%','&','(',')','*','+',',','/',':',';','>','<','=','?','[','\\',']','^','_','`','{','|','}','~'];
$replace_symbols = ['”','＃','＄','％','＆','（','）','＊','＋','，','／','：','；','＞','＜','＝','？','［','￥','］','＾','＿','｀','｛','｜','｝','～'];
$disallow_character = ['\\', '"', '/', PHP_EOL, "\r"];
$replace_character = ['\u005c', '\u0022', '\u002f', '\n'];
$i = 1;
foreach (glob($folder. '*', GLOB_ONLYDIR+GLOB_NOSORT) as $dir)
{
	$dirname = basename($dir);
	if ($title[0] === $dirname) continue;
	if (is_dir($dir)) $title[0] = $dirname;
}
if (!is_dir($title_dir = $folder. $title[0])) mkdir($title_dir);
if (!is_file($af = $title_dir. '/a')) touch($af);
if (!is_file($bf = $title_dir. '/b')) touch($bf);
if (!is_file($cf = $title_dir. '/c')) touch($cf);
if (!is_file($df = $title_dir. '/d')) touch($df);
$fglob = glob($title_dir. '/[0-9]*');
natsort($fglob);
$end = end($fglob);
if ($d = filter_input(INPUT_POST, 'd')) if (is_dir($title_dir)) rename($title_dir, $folder. str_replace($disallow_symbols, $replace_symbols, $d));
if ($del = filter_input(INPUT_GET, 'del'))
{
	if (is_file($h = $title_dir. '/'. $del))
	{
		unlink($h);
		header('location: ./'. ($h === $end ? '' : '?last=1'));
	}
}
if (filter_has_var(INPUT_GET, 'last')) unlink($end);
if (filter_has_var(INPUT_GET, 'b'))
{
	$archive_name = time(). ' - '. $title[0]. '.tar';
	$archive = new PharData($archive_name);
	$archive->buildFromDirectory($title_dir);
	$archive->compress(Phar::GZ);
	unlink($archive_name);
}
if ($input = json_decode(file_get_contents('php://input') ?? null, true))
{
	if (isset($input['1'], $input['2']) && 1 === strlen($input['1']) && is_file($an = $title_dir. '/'. basename($input['1'])))
		file_put_contents($an, $input['2'], LOCK_EX);
	else
	{
		$b = 1 !== strlen($input['b']) ? '' : $input['b'];
		$e = $title_dir. '/'. str_replace($disallow_symbols, $replace_symbols, $input['a']);
		$f = $title_dir. '/'. strip_tags(basename($input['c']));
		if ('b' === $b && !is_file($e))
			touch($e. $delimiter);
		elseif ('k' === $b)
		{
			if (is_file($e))
				file_put_contents($e, $input['d'], LOCK_EX);
			else
			{
				$g = glob($f. '*');
				rename((isset($g[0]) ? $g[0] : $f), $e);
			}
		}
		elseif ('d' === $b)
		{
			@unlink(glob($f. '*')[0]);
			file_put_contents($e, $input['d'], LOCK_EX);
		}
	}
}
?><!doctype html><html lang=ja><head><meta charset=utf-8><title>localOutliner</title><meta name=viewport content="width=device-width,initial-scale=1"><style>*{box-sizing:border-box}body{background:#222;color:#ccc;margin-left:10%;margin-right:10%;margin-top:3em;margin-bottom:3em}article{background:rgba(0,0,0,.1);border:0;padding:1em;font-size:x-large;white-space:pre-wrap}button,label{border:0;cursor:pointer;opacity:.35;padding:.5em;transition:opacity.5s}button:hover,label:hover{opacity:1}[contenteditable]:focus{outline:0}ol{counter-reset:i;list-style-type:none;padding:0}li{margin-bottom:1em;padding:1em 2.5em;position:relative;transition:background.5s}h1,h2{font-family:serif}h1,h2,article,aside{font-weight:unset;line-height:1.8em}h2:before{cursor:move;content:"<?=sprintf($title[1], $title[2])?>";counter-increment:i}input,.hide{display:none}.hide~label{background-image:linear-gradient(135deg,transparent,red)}label{padding:.7em;z-index:2}aside{background-color:pink;transition:opacity.3s,width.5s,height.5s;width:0;height:0;padding:0;opacity:0;overflow-y:auto;white-space:pre-wrap;z-index:1}label[for=a]{background-color:lightskyblue}#aa{background-color:lightcyan;color:lightskyblue}label[for=b]{background-color:palevioletred}#ba{background-color:lavenderblush;color:palevioletred}label[for=c]{background-color:lightgreen}#ca{background-color:honeydew;color:lightgreen}label[for=d]{background-color:goldenrod}#da{background-color:lemonchiffon;color:goldenrod}li button{background-color:inherit;padding:1em;position:absolute;top:0;right:0}li label{padding:.5em;position:absolute;transform:rotate(45deg);top:3.1em;left:.85em;background-image:linear-gradient(-45deg,transparent,red);z-index:1}small{position:fixed;bottom:0;left:45%;margin:.5em}
</style></head><body><script>const d=document,b=d.body,asd="aside",lbl="label",ipt="input",copyright="localOutliner";window.onload=()=>{<?php
if ($fglob)
{
	foreach ($fglob as $file)
	{
		if (is_dir($file)) continue;
		$bfile = basename($file);
		$j = !preg_match('/^\d+./', $bfile) ? ',""' : ',"'. preg_replace('/^(\d+.).*?/', '', $bfile). '"';
		$k = (!is_file($file) || !filesize($file)) ? '' : ',"'. str_replace($disallow_character, $replace_character, file_get_contents($file)).'"';
		echo 'l(', $i, $j, $k, ');';
		++$i;
	}
}
if ($ad = glob($title_dir. '/[a-d]'))
	foreach ($ad as $afile) echo 'd.getElementById("', basename($afile), 'a").textContent="', str_replace($disallow_character, $replace_character, file_get_contents($afile)), '";';
?>
for(h=0,ls=localStorage.length;h<ls;++h){key=localStorage.key(h),item=JSON.parse(localStorage.getItem(key));if(1===key.length){el=d.getElementById(key+"a");if("undefined"!==typeof el&&null!==el){d.getElementById(key).checked=el.style.opacity=item?1:0,el.style.width=item?"<?=$aside[0]?>":0,el.style.height=item?"<?=$aside[1]?>":0,el.style.padding=item?"<?=$aside[2]?>":0,el.onkeydown=f=>{if(13===f.keyCode)fetch("./",{method:"post",cache:"no-cache",body:JSON.stringify({1:f.target.id[0],2:f.target.innerText})})}}}else{el=d.getElementById(key);if("undefined"!==typeof el&&null!==el){item?el.classList.remove("hide"):el.classList.add("hide"),el.nextSibling.checked=item?0:1}}}},b.appendChild(h1=d.createElement("h1")),h1.textContent=d.title="<?=$title[0]?>",h1.contentEditable=true,h1.style.marginBottom="1em",h1.focus(),h1.onkeydown=e=>{if(13===e.keyCode){t=e.target.textContent,e.preventDefault();if(200<t.length)e.target.style.color="red";else{e.target.style.color="inherit",fd.append("d",t),fetch("./",{method:"post",cache:"no-cache",body:fd})}}},b.appendChild(ol=d.createElement("ol")),b.appendChild(btn=d.createElement("button")),btn.textContent="<?=$icon[0]?>",btn.style.margin="1em",b.appendChild(bkbtn=d.createElement("button")),bkbtn.textContent="<?=$icon[1]?>",i=<?=$i?>,fd=new FormData(),btn.onclick=()=>{f(i,"b"),l(i),++i},bkbtn.onclick=()=>{if(confirm("<?=sprintf($confirm[1], $title_dir)?>"))fetch("./?b=1",{method:"get",cache:"no-cache"}).then(()=>location.href="./")},d.ondragstart=({target})=>{dragged=target,id=target.id,list=target.parentNode.children;for(i=0,l=list.length;i<l;i+=1)if(dragged===list[i])index=i},d.ondragover=e=>e.preventDefault(),d.ondrop=({target})=>{if("l"===target.className&&id!==target.id){let drop;dragged.remove(dragged);for(i=0,l=list.length;i<l;i+=1)if(target===list[i])drop=i;if(index>drop)target.before(dragged);else target.after(dragged);for(j=0,m=list.length;j<m;j++)f((j+1)+"<?=$delimiter?>"+list[j].children[0].textContent,"d",(j+1)+"<?=$delimiter?>",list[j].childNodes[1].innerText)}},b.appendChild(abtn=d.createElement(ipt)),b.appendChild(bbtn=d.createElement(ipt)),b.appendChild(cbtn=d.createElement(ipt)),b.appendChild(dbtn=d.createElement(ipt)),b.appendChild(abel=d.createElement(lbl)),b.appendChild(bbel=d.createElement(lbl)),b.appendChild(cbel=d.createElement(lbl)),b.appendChild(dbel=d.createElement(lbl)),b.appendChild(aa=d.createElement(asd)),b.appendChild(ba=d.createElement(asd)),b.appendChild(ca=d.createElement(asd)),b.appendChild(da=d.createElement(asd)),abel.style.position=bbel.style.position=cbel.style.position=dbel.style.position=aa.style.position=ba.style.position=ca.style.position=da.style.position="fixed",abel.style.top=abel.style.left=aa.style.top=aa.style.left=bbel.style.top=bbel.style.right=ba.style.top=ba.style.right=cbel.style.left=cbel.style.bottom=ca.style.left=ca.style.bottom=dbel.style.right=dbel.style.bottom=da.style.right=da.style.bottom=0,abtn.type=bbtn.type=cbtn.type=dbtn.type="checkbox",abtn.id="a",bbtn.id="b",cbtn.id="c",dbtn.id="d",aa.id="aa",ba.id="ba",ca.id="ca",da.id="da",abel.htmlFor="a",bbel.htmlFor="b",cbel.htmlFor="c",dbel.htmlFor="d",aa.contentEditable=ba.contentEditable=ca.contentEditable=da.contentEditable=true,[abtn,bbtn,cbtn,dbtn].forEach(c=>c.onchange=e=>{ta=d.getElementById(e.target.id+"a"),style=ta.style;if(e.target.checked){style.width="<?=$aside[0]?>",style.height="<?=$aside[1]?>",style.padding="<?=$aside[2]?>",style.opacity=1,ta.focus(),ta.onkeydown=f=>{if(13===f.keyCode)fetch("./",{method:"post",cache:"no-cache",body:JSON.stringify({1:f.target.id[0],2:f.target.innerText})})},localStorage.setItem(e.target.id,e.target.checked)}else{style.width=style.height=style.padding=style.opacity=0,localStorage.setItem(e.target.id,e.target.checked)}}),b.appendChild(small=d.createElement("small")),small.textContent="\u00A9 "+new Date().toISOString().substring(0,4)+" "+copyright,d.title+=" - "+copyright;function f(a,b,c="",d=""){fetch("./",{method:"post",cache:"no-cache",body:JSON.stringify({a,b,c,d})})}function l(i,j="",k=""){ol.appendChild(li=d.createElement("li")),li.appendChild(h2=d.createElement("h2")),li.appendChild(a=d.createElement("article")),li.appendChild(inp=d.createElement("input")),li.appendChild(lb=d.createElement("label")),li.appendChild(hbtn=d.createElement("button")),hbtn.textContent="<?=$icon[2]?>",inp.type="checkbox",inp.id=lb.htmlFor="i-"+i,li.draggable=true,li.className="l",li.id="l"+i,h2.className="h2-"+i,h2.contentEditable=true,h2.textContent=j,h2.onkeydown=e=>{if(13===e.keyCode){e.preventDefault();if(200<e.target.textContent.length)e.target.style.color="red";else{e.target.style.color="inherit",f(i+"<?=$delimiter?>"+e.target.textContent,"k",i+"<?=$delimiter?>",e.target.nextSibling.innerText)}}},a.className=a.id=lb.dataset.id="a-"+i,a.contentEditable=true,a.textContent=k,a.onkeydown=e=>{if(13===e.keyCode)f(i+"<?=$delimiter?>"+e.target.previousSibling.textContent,"k",i+"<?=$delimiter?>",e.target.innerText)},hbtn.onclick=e=>{if(confirm("<?=$confirm[0]?>")){list=e.target.closest("ol").children,n=Array.from(list).indexOf(e.target.parentNode),fetch("./?del="+(n+1)+"<?=$delimiter?>"+list[n].children[0].textContent,{method:"get",cache:"no-cache"}),e.target.parentNode.remove();for(k=0,l=list.length;k<l;k++)f((k+1)+"<?=$delimiter?>"+list[k].children[0].textContent,"d",(k+1)+"<?=$delimiter?>",list[k].childNodes[1].innerText)}},hbtn.onmouseover=e=>e.target.parentNode.style.backgroundColor="rgba(0,0,0,.1)",hbtn.onmouseout=e=>e.target.parentNode.style.backgroundColor="inherit",lb.onclick=e=>{d.getElementById(e.target.dataset.id).classList.toggle("hide"),localStorage.setItem(e.target.dataset.id,d.getElementById(e.target.htmlFor).checked)}}</script></body></html>