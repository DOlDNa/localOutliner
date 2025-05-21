<?php
$title = ['タイトル', '第"%s"話　', 'counter(i,cjk-ideographic)', '新規タイトル', 'リカバリー'];
$confirm = ['削除しますか？', '「%s」を保存しますか？', '「%s」のデータに戻しますか？'];
$memo = ['350px', '450px', '2em'];
$backup_files = 20;
$icon = ['➕', '📥', '❌', '🔻', '🔺'];
$separator = '.';
$disallow_symbols = ['"','#','$','%','&','(',')','*','+',',','/',':',';','>','<','=','?','[','\\',']','^','_','`','{','|','}','~'];
$replace_symbols = ['”','＃','＄','％','＆','（','）','＊','＋','，','／','：','；','＞','＜','＝','？','［','￥','］','＾','＿','｀','｛','｜','｝','～'];
$disallow_character = ['\\', '"', '/', PHP_EOL, "\r"];
$replace_character = ['\u005c', '\u0022', '\u002f', '\n'];
$folder = !filter_has_var(INPUT_GET, 'folder') ? '' : str_replace($disallow_symbols, $replace_symbols, filter_input(INPUT_GET, 'folder'));
$del = !filter_has_var(INPUT_GET, 'del') ? '' : basename(filter_input(INPUT_GET, 'del'));
$del_chapter = !filter_has_var(INPUT_POST, 'del') ? '' : json_decode(filter_input(INPUT_POST, 'del'));
if ($glob_folder = glob('*', GLOB_ONLYDIR|GLOB_NOSORT)) usort($glob_folder, function ($a, $b) {return filemtime($b) - filemtime($a);});
$title[0] = basename($folder ?? $glob_folder[0] ?: $title[0]);
if (filter_has_var(INPUT_GET, 'backup'))
{
	if (!is_dir($backup_folder = $title[0]. '/0/')) mkdir($backup_folder);
	$filename = $backup_folder. time(). '.tar.gz';
	`tar -czf "$filename" --exclude='0' "$title[0]"`;
	if ($glob_backup_folder = glob($backup_folder. '*'))
	{
		if ($backup_files < count($glob_backup_folder)) unlink($glob_backup_folder[0]);
	}
}
if ($recover = filter_input(INPUT_GET, 'recover', FILTER_SANITIZE_NUMBER_INT))
{
	if (is_file($recover_gz = $title[0]. '/0/'. basename($recover). '.tar.gz'))
	{
		foreach (glob($title[0]. '/*', GLOB_NOSORT) as $g) if (is_file($g)) unlink($g);
		`tar -C ./ -xf "$recover_gz"`;
	}
}
if ($rename = filter_input(INPUT_POST, 'rename'))
{
	$replace_name = str_replace($disallow_symbols, $replace_symbols, $rename);
	rename($title[0], $replace_name);
	touch($replace_name);
}
elseif ($input = json_decode(file_get_contents('php://input') ?? null, true))
{
	if (isset($input['1'], $input['2']) && is_file($an = $title[0]. '/'. $input['1'][0]))
	{
		file_put_contents($an, $input['2'], LOCK_EX);
	}
	elseif (isset($input['a'], $input['b']))
	{
		$e = $title[0]. '/'. str_replace($disallow_symbols, $replace_symbols, $input['a']);
		if ('b' === $input['b'] && !is_file($e))
		{
			touch($e. $separator);
		}
		elseif ('k' === $input['b'])
		{
			if (isset($input['d']) && is_file($e))
			{
				file_put_contents($e, $input['d'], LOCK_EX);
			}
			elseif (isset($input['c']))
			{
				$f = $title[0]. '/'. basename($input['c']);
				$g = glob($f. '*')[0] ?? '';
				rename((isset($g) ? $g : $f), $e);
			}
		}
	}
}
elseif (!is_dir($glob_folder[0]) && !is_dir($title[0]))
{
	mkdir($title[0]);
	header('location: ./'. urlencode($title[0]). '/');
}
elseif (is_dir($glob_folder[0]) && !$folder && !$del) header('location: ./'. urlencode($glob_folder[0]). '/');
elseif (!is_dir($title[3]) && $folder === $title[3]) mkdir($title[3]);
if (is_dir($title[0]))
{
	if (!is_file($af = $title[0]. '/a')) touch($af);
	if (!is_file($bf = $title[0]. '/b')) touch($bf);
	if (!is_file($cf = $title[0]. '/c')) touch($cf);
	if (!is_file($df = $title[0]. '/d')) touch($df);
}
$fglob = glob($title[0]. '/[1-9]*', GLOB_NOSORT);
$c = count($fglob);
natsort($fglob);
if ($c !== (int)explode($separator, basename($last = end($fglob)))[0]) unlink($last);
if ($del && is_dir($del)) `rm -r ./"$del"`;
if (is_array($del_chapter))
{
	foreach ($fglob as $old_chapter) unlink($old_chapter);
	foreach ($del_chapter as $dc) file_put_contents($folder. '/'. basename($dc[0]), $dc[1], LOCK_EX);
}
?><!doctype html><html lang=ja><head><meta charset=utf-8><title>localOutliner</title><meta name=viewport content="width=device-width,initial-scale=1"><style>.btn{background-color:#fff;bottom:0px;height:1rem;left:1rem;padding:0px;position:absolute;width:2rem}.hide~label[for^="e-"]:before{content:"<?=$icon[3]?>"}.mark{position:absolute;right:2.5rem;bottom:1rem}.memo{height:0;line-height:1.8em;opacity:0;overflow-y:auto;padding:0;transition:opacity.3s,width.5s,height.5s;white-space:pre-wrap;width:0;z-index:2}aside button,select{padding:.5em}button,label,mark,#recover{cursor:pointer;opacity:.35;transition:opacity.5s}button,[contenteditable]:focus,textarea,select{border:0;outline:0}button:hover,label:hover,button:focus,label:focus,select:focus,mark:hover,#recover:hover{opacity:1}div label{background:black;padding:.5em;z-index:1}div[id]{display:flex;position:relative}fieldset>button{position:absolute;top:0;right:0}fieldset.dragging{opacity:.7}fieldset[draggable=true]{cursor:row-resize}fieldset{border:0;margin-bottom:1em;padding:1em 2.5em;position:relative;transition:background.5s}footer{bottom:0;font-size:small;left:45%;margin:.5em;position:fixed}h1,h2,textarea{font-weight:unset}h1,h2{font-family:serif;padding-left:.5em;padding-right:.5em}h2:before{content:"<?=sprintf($title[1], $title[2])?>";counter-increment:i}header>button,fieldset>button{background-color:inherit;padding:1em}header{display:flex;align-items:start;justify-content:space-between;transition:background.5s}input,.hide{display:none!important}label[for^="e-"]:before{content:"<?=$icon[4]?>"}label[for^="e-"]{font-size:x-large;padding:0;position:absolute;top:1.4em;left:.5em;z-index:1}label{padding:.7em;z-index:3}mark{background:#e9e9ed;cursor:unset;margin-left:.7em;padding:.62em .5em;font-size:small}mark:after{content:"文字"}main{counter-reset:i;padding:0}option{color:dimgray}samp b{font-weight:unset;text-emphasis-style:dot}samp i{font-style:normal;text-combine-upright:all}samp{background-color:rebeccapurple;column-fill:auto;column-gap:4em;columns:20em;font-family:serif;font-size:x-large;height:100%;letter-spacing:.05em;line-height:2;outline:0;overflow-y:scroll;overscroll-behavior-y:none;padding:1em;position:absolute;white-space:pre-wrap;width:100%;word-break:break-word;writing-mode:vertical-rl}textarea{background:rgba(0,0,0,.1);color:inherit;font-family:unset;font-size:large;height:500px;letter-spacing:.05em;line-height:2em;padding:1em;width:100%}#select{background-color:rgba(0,0,0,.1);color:inherit;position:fixed;top:0;left:45%;width:10em;overflow:hidden}*{box-sizing:border-box}::-webkit-scrollbar,::-webkit-scrollbar-corner,::-webkit-resizer{background:inherit}::-webkit-scrollbar-thumb{background:#ccc;border-radius:5px}[contenteditable]:focus{cursor:auto}</style><link rel="icon" href="<?=dirname(getenv('SCRIPT_NAME'))?>/icon.svg" type="image/svg+xml" sizes=any></head><body style="background:#222;color:#ccc;margin-left:10%;margin-right:10%;margin-top:3em;margin-bottom:3em"><script>d=document,copyright=d.title,btn=d.createElement("button"),inp=d.createElement("input"),lbl=d.createElement("label"),slt=d.createElement("select"),txa=d.createElement("textarea"),mark=d.createElement("mark"),ja=new Intl.Segmenter("ja",{granularity:"grapheme"}),d.body.appendChild(header=d.createElement("header")),header.appendChild(h1=d.createElement("h1")),h1.textContent=d.title="<?=$title[0]?>",h1.contentEditable=true,header.appendChild(h1btn=btn.cloneNode(true)),h1btn.textContent="<?=$icon[2]?>",h1.accessKey="h",h1btn.accessKey="i",h1.oninput=e=>border(e),h1.onkeydown=e=>{nl=n(e.target.textContent),e.target.style.color=nl[1];if("Enter"===e.key||"NumpadEnter"===e.key){t=e.target.textContent,e.preventDefault();if(nl[0]){fd.append("rename",t),f2()}}},h1btn.onmouseover=e=>header.style.backgroundColor="rgba(255,0,0,.1)",h1btn.onmouseout=e=>header.style.backgroundColor="inherit",h1btn.onfocus=e=>header.style.backgroundColor="rgba(255,0,0,.1)",h1btn.onblur=e=>header.style.backgroundColor="inherit",h1btn.onclick=e=>{if(confirm("<?=$confirm[0]?>"))fetch("./del").then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../")},d.body.appendChild(main=d.createElement("main")),d.body.appendChild(aside=d.createElement("aside")),aside.appendChild(ebtn=btn.cloneNode(true)),ebtn.textContent="<?=$icon[0]?>",ebtn.accessKey="e",ebtn.style.margin="1em",aside.appendChild(bkbtn=btn.cloneNode(true)),bkbtn.textContent="<?=$icon[1]?>",bkbtn.accessKey="f",i=<?=1+$c?>,fd=new FormData(),ebtn.onclick=()=>{fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i,b:"b"})}).then(r=>{if(r.ok)location.reload()}).catch(()=>location.href="../"),l(i),d.querySelector("#d-"+i).classList.remove("hide"),location.hash="c-"+i,d.querySelector("#a-"+i).focus(),i++},bkbtn.onclick=()=>{if(confirm("<?=sprintf($confirm[1], $title[0])?>"))fetch("./backup").then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../")},ondragstart=({target})=>{dragged=target,id=target.id,dlist=main.children,localStorage.clear();for(i1=0,l1=dlist.length;i1<l1;i1++){if(dragged===dlist[i1])idx=i1}},ondragover=({target,drop})=>{if("FIELDSET"===target.tagName&&id!==target.id){for(i2=0,l2=dlist.length;i2<l2;i2++){if(false===dlist[i2].querySelector("div").classList.contains("hide"))dlist[i2].querySelector("div").classList.add("hide");if(target===dlist[i2])drop=i2;if(idx>=drop)target.before(dragged);else target.after(dragged)}}},ondragend=({target})=>{if("TEXTAREA"!==target.tagName){list=main.children,dlist=[];for(i3=0,l3=list.length;i3<l3;i3++)dlist.push([1+i3+"<?=$separator?>"+list[i3].querySelector("h2").textContent,list[i3].querySelector("textarea").value]);fd.append("del",JSON.stringify(dlist)),f2()}},aside.appendChild(abtn=inp.cloneNode(true)),aside.appendChild(bbtn=inp.cloneNode(true)),aside.appendChild(cbtn=inp.cloneNode(true)),aside.appendChild(dbtn=inp.cloneNode(true)),aside.appendChild(abel=lbl.cloneNode(true)),aside.appendChild(bbel=lbl.cloneNode(true)),aside.appendChild(cbel=lbl.cloneNode(true)),aside.appendChild(dbel=lbl.cloneNode(true)),aside.appendChild(aa=txa.cloneNode(true)),aside.appendChild(ba=txa.cloneNode(true)),aside.appendChild(ca=txa.cloneNode(true)),aside.appendChild(da=txa.cloneNode(true)),abel.style.position=bbel.style.position=cbel.style.position=dbel.style.position=aa.style.position=ba.style.position=ca.style.position=da.style.position="fixed",abel.style.top=abel.style.left=aa.style.top=aa.style.left=bbel.style.top=bbel.style.right=ba.style.top=ba.style.right=cbel.style.left=cbel.style.bottom=ca.style.left=ca.style.bottom=dbel.style.right=dbel.style.bottom=da.style.right=da.style.bottom=0,abtn.type=bbtn.type=cbtn.type=dbtn.type="checkbox",abel.style.backgroundColor="royalblue",bbel.style.backgroundColor="palevioletred",cbel.style.backgroundColor="forestgreen",dbel.style.backgroundColor="goldenrod",abtn.id="a",bbtn.id="b",cbtn.id="c",dbtn.id="d",aa.id="aa",ba.id="ba",ca.id="ca",da.id="da",aa.style.color="royalblue",ba.style.color="palevioletred",ca.style.color="forestgreen",da.style.color="goldenrod",aa.style.backgroundColor="lightcyan",ba.style.backgroundColor="lavenderblush",ca.style.backgroundColor="honeydew",da.style.backgroundColor="lemonchiffon",aa.className=ba.className=ca.className=da.className="memo",abel.htmlFor=abtn.accessKey="a",bbel.htmlFor=bbtn.accessKey="b",cbel.htmlFor=cbtn.accessKey="c",dbel.htmlFor=dbtn.accessKey="d",[abtn,bbtn,cbtn,dbtn].forEach(c=>c.onchange=e=>{memo=d.getElementById(e.target.id+"a"),style=memo.style,style.transition="opacity .3s,width .5s,height .5s";if(e.target.checked){style.width="<?=$memo[0]?>",style.height="<?=$memo[1]?>",style.padding="<?=$memo[2]?>",style.opacity=1,memo.focus(),memo.onkeydown=e=>{if(e.shiftKey&&"Space"===e.code)e.preventDefault()},memo.onkeyup=f=>{tab(f),f1(f)},memo.onblur=f=>f1(f)}else{style.width=style.height=style.opacity=0,memo.blur();if(null!==(dcn=d.querySelector("div[class=\"\"]")))dcn.querySelector("textarea").focus()}localStorage.setItem(e.target.id,e.target.checked)}),d.body.appendChild(footer=d.createElement("footer")),footer.textContent="\u00a9 "+new Date().toISOString().substring(0,4)+" "+copyright,d.title+=" - "+copyright,l=(i,j="",k="",m=0)=>{main.appendChild(fieldset=d.createElement("fieldset")),fieldset.appendChild(h2=d.createElement("h2")),fieldset.appendChild(finp=inp.cloneNode(true)),fieldset.appendChild(div=d.createElement("div")),div.id="d-"+i,div.appendChild(plb=lbl.cloneNode(true)),div.appendChild(pinp=inp.cloneNode(true)),div.appendChild(textarea=txa.cloneNode(true)),tp=btn.cloneNode(true),ds=btn.cloneNode(true),sb=btn.cloneNode(true),db=btn.cloneNode(true),div.appendChild(samp=d.createElement("samp")),fieldset.appendChild(flb=lbl.cloneNode(true)),fieldset.appendChild(h2btn=btn.cloneNode(true)),h2btn.textContent="<?=$icon[2]?>",finp.type=pinp.type="checkbox",finp.id=flb.htmlFor="e-"+i,flb.tabIndex=1,flb.dataset.accessKey=i,flb.onkeydown=e=>{if("Enter"===e.key)d.activeElement.click()},pinp.id=plb.htmlFor="p-"+i,fieldset.draggable=true,fieldset.id="c-"+i,h2.contentEditable=true,h2.textContent=j,textarea.id="a-"+i,samp.id="pp-"+i,div.classList.add("hide"),samp.classList.add("hide"),textarea.innerHTML=k,fieldset.appendChild(fmark=mark.cloneNode(true)),fmark.className="mark",fmark.appendChild(fspan=document.createElement("span")),fspan.id="s-"+i,d.querySelector("#s-"+i).innerText=m,h2.oninput=e=>border(e),h2.onkeyup=e=>{nl=n(e.target.textContent),e.target.style.color=nl[1];if("Enter"===e.key||"NumpadEnter"===e.key){e.preventDefault();if(nl[0])fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i+"<?=$separator?>"+e.target.textContent,b:"k",c:i+"<?=$separator?>",d:e.target.closest("fieldset").querySelector("textarea").value})}).then(r=>{if(r.ok)e.target.style.borderColor="transparent"}).then(()=>location.href="../#c-"+i).catch(()=>location.href="../")}},textarea.onfocus=e=>{e.target.previousSibling.accessKey="p",tp.className=ds.className=sb.className=db.className="btn",e.target.parentNode.appendChild(tp),tp.textContent="…",tp.style.left="1rem",tp.accessKey="x",tp.onclick=b=>{add(b,e),f3(e,i)},e.target.parentNode.appendChild(ds),ds.textContent="―",ds.style.left="3rem",ds.accessKey="w",ds.onclick=b=>{add(b,e),f3(e,i)},e.target.parentNode.appendChild(sb),sb.textContent="《》",sb.style.left="5rem",sb.accessKey="y",sb.onclick=b=>{add(b,e),f3(e,i)},e.target.parentNode.appendChild(db),db.textContent="《》²",db.style.left="7rem",db.accessKey="z",db.onclick=b=>{add(b,e),f3(e,i)}},textarea.onblur=e=>{e.target.previousSibling.removeAttribute("accessKey"),f3(e,i)},textarea.oninput=e=>{border(e),countall()},textarea.onkeydown=e=>{if(e.shiftKey&&"Space"===e.code)e.preventDefault()},textarea.onkeyup=e=>{d.querySelector("#s-"+i).innerText=Array.from(ja.segment(e.target.value.replace(/[\n\s]/g,""))).length.toLocaleString("ja-JP"),tab(e);if("Enter"===e.key||"NumpadEnter"===e.key)f3(e,i)},h2btn.onclick=e=>{if(confirm("<?=$confirm[0]?>")){fetch("./del/"+i+"<?=$separator?>"+e.target.closest("fieldset").querySelector("h2").textContent.replace(/ /g,"+")).then(r=>{if(r.ok){e.target.parentNode.remove();list=main.children,plist=[];for(i4=0,l4=list.length;i4<l4;i4++)plist.push([1+i4+"<?=$separator?>"+list[i4].querySelector("h2").textContent,list[i4].querySelector("textarea").value]);fd.append("del",JSON.stringify(plist)),f2()}}).catch(()=>location.href="../")}},h2btn.onmouseover=e=>e.target.parentNode.style.backgroundColor="rgba(255,0,0,.1)",h2btn.onmouseout=e=>e.target.parentNode.style.backgroundColor="inherit",h2btn.onfocus=e=>e.target.parentNode.style.backgroundColor="rgba(255,0,0,.1)",h2btn.onblur=e=>e.target.parentNode.style.backgroundColor="inherit",finp.onchange=e=>{if(false===e.target.nextSibling.classList.contains("hide")){location.hash="",e.target.nextSibling.closest("fieldset").draggable=true,localStorage.setItem(e.target.nextSibling.id,false)}else{location.hash="c-"+i,e.target.nextSibling.closest("fieldset").draggable=false,localStorage.setItem(e.target.nextSibling.id,true),setTimeout(()=>d.querySelector("#a-"+i).focus(),5)}e.target.nextSibling.classList.toggle("hide")},pinp.onchange=e=>{d.querySelector("#p"+e.target.id).classList.toggle("hide"),d.querySelector("#p"+e.target.id).innerHTML=e.target.nextSibling.value.replace(/([｜\|])?(《《(.*?)》》)/gu,(b,b1,b2,b3)=>{if(b1)return b2;else return"<b>"+b3+"<\/b>"}).replace(/[｜\|]([^《]+)《([^》]+)》/g,"<ruby>$1<rt>$2<\/rt><\/ruby>").replace(/([\p{sc=Han}]+)《(.*?)》/gu,"<ruby>$1<rt>$2<\/rt><\/ruby>").replace(/[｜\|]([\p{scx=Hira}\p{scx=Kana}\p{scx=Han}]+)/gu,"$1").replace(/(\d{5,})/g,num=>{const numeral=["○","一","二","三","四","五","六","七","八","九"],unit=["零","十","百","千","万","億","兆","京","垓"];if(num.length/4>unit.length-3)return num.replace(/¥d/g,s=>numeral[parseInt(s)]);let kn,str="",arr=num.match(/\d{1,4}?(?=(\d{4})*$)/gm);for(i5=0,l5=arr.length-1;i5<=l5;i5++){if(!parseInt(arr[i5]))continue;for(i6=0,l6=arr[i5].length-1;i6<=l6;i6++){kn=parseInt(arr[i5][i6]);if(!kn)continue;if(i6===l6)str+=numeral[kn];else str+=(1<kn?numeral[kn]:"")+unit[l6-i6]}if(i5!==l5)str+=unit[l5-i5+3]}return str}).replace(/(\d{3}\,\d{3}|\d{2,4}|\d\.\d{1,4}|\.\d{1,4}|\d| [A-Za-z] )/g,"<i>$1<\/i>").replace(/(?!<\/?b>|<\/?i>|<\/?rt>|<\/?ruby>)<([^>]+)>/gi,"&lt$1&gt").replace(/！？/g,"⁉").split("\n").map(line=>{return (line.match(/^\s/)?"":"　")+line}).join("\n"),d.querySelector("#p"+e.target.id).tabIndex=0,d.querySelector("#p"+e.target.id).focus(),d.querySelector("#p-"+i).accessKey="p";if(d.querySelector("#p"+e.target.id).classList.contains("hide"))e.target.nextSibling.focus()}},n=(s,l=0)=>{for(i in s)if(s[i].match(/[^\x01-\x7e\uff65-\uff9f]/))l+=3;else l+=1;return 240>=l?[true,"inherit"]:[false,"red"]},tab=e=>{if(e.shiftKey&&"Space"===e.code){start=e.target.selectionStart,end=e.target.selectionEnd,e.target.value=e.target.value.substring(0,start)+"\t"+e.target.value.substring(end),e.target.selectionStart=e.target.selectionEnd=1+start}},f1=e=>{if("blur"===e.type||"Enter"===e.key||"NumpadEnter"===e.key)fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({1:e.target.id[0],2:e.target.value})}).then(r=>{if(r.ok){e.target.style.borderColor="transparent",localStorage.setItem("c1",e.target.selectionStart)}}).catch(()=>location.href="../")},f2=()=>fetch("",{method:"post",cache:"no-cache",body:fd}).then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../"),f3=(e,i)=>fetch("",{method:"post",cache:"no-cache",body:JSON.stringify({a:i+"<?=$separator?>"+e.target.closest("fieldset").querySelector("h2").textContent,b:"k",c:i+"<?=$separator?>",d:e.target.value})}).then(r=>{if(r.ok){e.target.style.borderColor="transparent",localStorage.setItem("c0",e.target.selectionStart)}}).catch(()=>location.href="../"),border=e=>{e.target.style.transition="border-color.3s",e.target.style.border="thin solid darkorange"},add=(b,e)=>{let pos=getRange(e.target),val=e.target.value,range=val.slice(pos.start,pos.end),beforeNode=val.slice(0,pos.start),afterNode=val.slice(pos.end),insertNode,cpos;if(tp===b.target)insertNode="……",cpos=pos.end+insertNode.length;if(ds===b.target)insertNode="――",cpos=pos.end+insertNode.length;if(sb===b.target){if(pos.start!==pos.end)insertNode="｜"+range+"《》",cpos=pos.end+2;else insertNode="《》",cpos=pos.end+insertNode.length-1}if(db===b.target){if(pos.start!==pos.end)insertNode="《《"+range+"》》",cpos=pos.start+range.length+4;else insertNode="《《》》",cpos=pos.start+2}e.target.value=beforeNode+insertNode+afterNode,e.target.setSelectionRange(cpos,cpos),e.target.focus()},getRange=obj=>{const pos=new Object();if(window.getSelection())pos.start=obj.selectionStart,pos.end=obj.selectionEnd;return pos},<?php
if ($fglob)
{
	$i = 1;
	$l = 0;
	foreach ($fglob as $file)
	{
		if (is_file($file))
		{
			$bfile = basename($file);
			$ff = file_get_contents($file);
			$j = !preg_match('/^\d+./', $bfile) ? '' : preg_replace('/^(\d+.).*?/', '', $bfile);
			$k = !filesize($file) ? '' : str_replace($disallow_character, $replace_character, $ff);
			$l += $m = mb_strlen(str_replace([PHP_EOL, ' ', '　'], '', $ff), 'UTF-8');
			$m = number_format($m);
			echo "l(\"$i\",\"$j\",\"$k\",\"$m\"),";
			++$i;
		}
	}
}
if ($ad = glob($title[0]. '/[a-d]'))
	foreach ($ad as $afile)
	{
		$baf = basename($afile);
		echo $baf, 'a.textContent="', str_replace($disallow_character, $replace_character, file_get_contents($afile)), '",', $baf, 'a.oninput=e=>{e.target.style.transition="border-color.3s",e.target.style.border="thin solid red"},';
	}
if ($glob_folder)
{
	echo 'header.appendChild(hslt=slt.cloneNode(true)),hslt.id="select",hslt.accessKey="s",hslt.tabIndex="-1",';
	foreach ($glob_folder as $key => $val)
	{
		$dirname = basename($val);
		echo 'hslt.add(opt', $key, '=new Option("', $dirname, '")),';
		if ($title[0] === $dirname) echo 'opt', $key, '.setAttribute("selected","selected"),';
	}
	if (!is_dir($title[3])) echo 'hslt.appendChild(hr=d.createElement("hr")),hslt.add(opt', (1+$key), '=new Option("', $title[3], '")),opt', (1+$key), '.style.color="forestgreen",';
	echo 'hslt.onchange=e=>location.href="../"+encodeURI(e.target.value).replace(/%20/g,"+")+"/";';
}
if ($gzs = glob($title[0]. '/0/[1-9][7-9][1-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]\.tar.gz', GLOB_NOSORT))
{
	rsort($gzs);
	echo 'aside.appendChild(aslt=slt.cloneNode(true)),aslt.accessKey="r",aslt.id="recover",aslt.style.marginLeft=".7em",aslt.add(new Option("', $title[4], '")),aslt.firstChild.disabled=true,';
	foreach ($gzs as $gz)
	{
		$gzname = basename($gz, '.tar.gz');
		echo 'aslt.add(new Option("', date('Y-m-d H:i:s', $gzname), '","', $gzname, '")),';
	}
	echo 'aslt.onchange=e=>{if(confirm("', sprintf($confirm[2], date('Y-m-d H:i:s', $gzname)), '"))fetch("./recover/"+e.target.value).then(r=>{if(r.ok)location.href="../"}).catch(()=>location.href="../")};';
}
?>mark.innerHTML="合計：<span id=totalnum><?=number_format($l)?></span>",aside.appendChild(mark),countall=()=>{let total=0;d.querySelectorAll("textarea[id^=a-]").forEach(ta=>total+=Array.from(ja.segment(ta.value.replace(/[\n\s]/g,""))).length),totalnum.textContent=total.toLocaleString("ja-JP")};for(i7=0,l7=localStorage.length;i7<l7;i7++){if(key=localStorage.key(i7))if(key[0].match(/[a-d]/)){item=JSON.parse(localStorage.getItem(key)),el=d.getElementById(key+"a"),em=d.getElementById(key);if(1===key.length){if(el){em.checked=el.style.opacity=item?1:0,el.style.width=item?"<?=$memo[0]?>":0,el.style.height=item?"<?=$memo[1]?>":0,el.style.padding=item?"<?=$memo[2]?>":0;if(c1=localStorage.getItem("c1"))el.setSelectionRange(c1,c1);el.onkeydown=e=>{if(e.shiftKey&&"Space"===e.code)e.preventDefault()},el.onkeyup=e=>{tab(e),f1(e)},el.onblur=e=>f1(e)}}else{if(em){if(item){em.classList.remove("hide"),em.previousSibling.checked=true,em.closest("fieldset").draggable=false;if(c0=localStorage.getItem("c0"))em.querySelector("textarea").setSelectionRange(c0,c0)}else{em.classList.add("hide"),em.previousSibling.checked=false,em.closest("fieldset").draggable=true}}}}}if(hash=location.hash){if(null!==(qh=d.querySelector(hash))){if((qdiv=qh.querySelector("div")).classList.contains("hide"))qdiv.classList.remove("hide");setTimeout(()=>qh.querySelector("textarea").focus(),5)}}let currentInput="",timeout;d.body.onkeydown=e=>{if(e.shiftKey&&e.altKey){if(e.code.startsWith("Digit")||e.code.startsWith("Numpad")){const key=e.code.replace("Digit","").replace("Numpad","");if(!isNaN(key)){currentInput+=key;clearTimeout(timeout);timeout=setTimeout(()=>{const ldak=d.querySelector(`label[data-access-key="${currentInput}"]`);if(ldak)ldak.click();currentInput=""},1200)}}}}</script></body></html>
