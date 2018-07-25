/* --- Swazz Javascript Calendar ---
/* --- v 1.0 3rd November 2006
By Oliver Bryant
http://calendar.swazz.org */

function getObj(objID)
{
    if (document.getElementById) {return document.getElementById(objID);}
    else if (document.all) {return document.all[objID];}
    else if (document.layers) {return document.layers[objID];}
}

function checkClick(e) {
        e?evt=e:evt=event;
        CSE=evt.target?evt.target:evt.srcElement;
        if (getObj('fc'))
                if (!isChild(CSE,getObj('fc')))
                        getObj('fc').style.display='none';
}

function isChild(s,d) {
        while(s) {
                if (s==d)
                        return true;
                s=s.parentNode;
        }
        return false;
}

function Left(obj)
{
        var curleft = 0;
        if (obj.offsetParent)
        {
                while (obj.offsetParent)
                {
                        curleft += obj.offsetLeft
                        obj = obj.offsetParent;
                }
        }
        else if (obj.x)
                curleft += obj.x;
        return curleft;
}

function Top(obj)
{
        var curtop = 0;
        if (obj.offsetParent)
        {
                while (obj.offsetParent)
                {
                        curtop += obj.offsetTop
                        obj = obj.offsetParent;
                }
        }
        else if (obj.y)
                curtop += obj.y;
        return curtop;
}
var monpath=LireCookie("monpath");
document.write('<table id="fc" style="position:absolute;border-collapse:collapse;background:#CEE6EC;border:1px solid #002D45;display:none;" cellpadding="2">');
document.write('<tr><td style="cursor:pointer;background:#CEE6EC;" onclick="csuby()"><img src="' + monpath + '/images/ptiflechg.gif" title="Année précédente"></td><td style="cursor:pointer;background:#CEE6EC;" onclick="csubm()"><img src="' + monpath + '/images/ptiflech2g.gif" title="Mois précédent"></td><td colspan=3 id="mns" align="center" style="font:bold 13px Arial;background:#CEE6EC;"></td><td align="right" style="cursor:pointer;background:#CEE6EC;" onclick="caddm()"><img src="' + monpath + '/images/ptiflech2d.gif" title="Mois suivant"></td><td align="right" style="cursor:pointer;background:#CEE6EC;" onclick="caddy()"><img src="' + monpath + '/images/ptiflechd.gif" title="Année suivante"></td></tr>');
document.write('<tr><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">L</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">M</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">M</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">J</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">V</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">S</td><td align=center style="background:#2B677A;color:#FFF;font:12px Arial">D</td></tr>');
for(var kk=1;kk<=6;kk++) {
        document.write('<tr>');
        for(var tt=1;tt<=7;tt++) {
                num=7 * (kk-1) - (-tt);
                document.write('<td id="v' + num + '" style="width:18px;height:18px">&nbsp;</td>');
        }
        document.write('</tr>');
}
document.write('</table>');

document.all?document.attachEvent('onclick',checkClick):document.addEventListener('click',checkClick,false);


// Calendar script
var now = new Date;
var sccm=now.getMonth();
var sccy=now.getFullYear();
var ccm=now.getMonth();
var ccy=now.getFullYear();

var updobj;
function lcs(ielem) {
        updobj=ielem;
        getObj('fc').style.left=Left(ielem);
        getObj('fc').style.top=Top(ielem)+ielem.offsetHeight;
        getObj('fc').style.display='';

        // First check date is valid
        curdt=ielem.value;
        curdtarr=curdt.split('/');
        isdt=true;
        for(var k=0;k<curdtarr.length;k++) {
                if (isNaN(curdtarr[k]))
                        isdt=false;
        }
        if (isdt&(curdtarr.length==3)) {
                ccm=curdtarr[1]-1;
                ccy=curdtarr[2];
                prepcalendar(curdtarr[0],curdtarr[1]-1,curdtarr[2]);
        }

}

function evtTgt(e)
{
        var el;
        if(e.target)el=e.target;
        else if(e.srcElement)el=e.srcElement;
        if(el.nodeType==3)el=el.parentNode; // defeat Safari bug
        return el;
}
function EvtObj(e){if(!e)e=window.event;return e;}
function cs_over(e) {
        evtTgt(EvtObj(e)).style.background='#D45211';
        evtTgt(EvtObj(e)).style.color='#FFFFFF';
}
function cs_out(e) {
        evtTgt(EvtObj(e)).style.background='#CEE6EC';
        evtTgt(EvtObj(e)).style.color='#333333';
}
function cs_click(e) {
        updobj.value=calvalarr[evtTgt(EvtObj(e)).id.substring(1,evtTgt(EvtObj(e)).id.length)];
        getObj('fc').style.display='none';

}

var mn=new Array('Janv','Fév','Mars','Avr','Mai','Juin','Juil','Aout','Sept','Oct','Nov','Déc');
var mnn=new Array('31','28','31','30','31','30','31','31','30','31','30','31');
var mnl=new Array('31','29','31','30','31','30','31','31','30','31','30','31');
var calvalarr=new Array(42);

function f_cps(obj) {
        obj.style.background='#CEE6EC';//obj.style.background='#C4D3EA';
        obj.style.font='10px Arial';
        obj.style.color='#333333';
        obj.style.textAlign='center';
        obj.style.textDecoration='none';
        obj.style.border='1px solid #6487AE';
        obj.style.cursor='pointer';
}

function f_cpps(obj) {
        obj.style.background='#CEE6EC';//obj.style.background='#C4D3EA';
        obj.style.font='10px Arial';
        obj.style.color='#ABABAB';
        obj.style.textAlign='center';
        /*obj.style.textDecoration='line-through';*/
        obj.style.border='1px solid #6487AE';
        obj.style.cursor='default';
}

function f_hds(obj) {
        obj.style.background='#D45211';   /*#FFF799*/
        obj.style.font='bold 10px Arial';
        obj.style.color='#FFFFFF';
        obj.style.textAlign='center';
        obj.style.border='1px solid #6487AE';
        obj.style.cursor='pointer';
}

// day selected
function prepcalendar(hd,cm,cy) {
        now=new Date();
        sd=now.getDate();
        td=new Date();
        td.setDate(7);
        td.setFullYear(cy);
        td.setMonth(cm);
        cd=td.getDay();
        getObj('mns').innerHTML=mn[cm]+ ' ' + cy;
        marr=((cy%4)==0)?mnl:mnn;
        for(var d=1;d<=42;d++) {
                f_cps(getObj('v'+parseInt(d)));
                if ((d >= (cd -(-1))) && (d<=cd-(-marr[cm]))) {
                        dip=((d-cd < sd)&&(cm==sccm)&&(cy==sccy));
                        htd=((hd!='')&&(d-cd==hd));
                        if (dip)
                                f_cpps(getObj('v'+parseInt(d)));
                        else if (htd)
                                f_hds(getObj('v'+parseInt(d)));
                        else
                                f_cps(getObj('v'+parseInt(d)));

                        getObj('v'+parseInt(d)).onmouseover=(dip)?cs_over:cs_over;//si oui null
                        getObj('v'+parseInt(d)).onmouseout=(dip)?cs_out:cs_out;
                        getObj('v'+parseInt(d)).onclick=(dip)?cs_click:cs_click;

                        getObj('v'+parseInt(d)).innerHTML=d-cd;
                        var ajout_jour = ((d-cd) < 10)? "0" : '';
                        var ajout_mois = ((cm-(-1)) < 10)? "0" : '';
                        calvalarr[d]=ajout_jour+(d-cd)+'/'+ajout_mois+(cm-(-1))+'/'+cy;
                }
                else {
                        getObj('v'+d).innerHTML='&nbsp;';
                        getObj('v'+parseInt(d)).onmouseover=null;
                        getObj('v'+parseInt(d)).onmouseout=null;
                        getObj('v'+parseInt(d)).style.cursor='default';
                        }
        }
}

prepcalendar('',ccm,ccy);
//getObj('fc'+cc).style.visibility='hidden';

function caddm() {
        marr=((ccy%4)==0)?mnl:mnn;

        ccm+=1;
        if (ccm>=12) {
                ccm=0;
                ccy++;
        }
        cdayf();
        prepcalendar('',ccm,ccy);
}

function csubm() {
        marr=((ccy%4)==0)?mnl:mnn;

        ccm-=1;
        if (ccm<0) {
                ccm=11;
                ccy--;
        }
        cdayf();
        prepcalendar('',ccm,ccy);
}
function caddy() {
        marr=((ccy%4)==0)?mnl:mnn;
         ccy++;
         cdayf();
         prepcalendar('',ccm,ccy);
}
function csuby() {
         marr=((ccy%4)==0)?mnl:mnn;
         ccy--;
         cdayf();
         prepcalendar('',ccm,ccy);
}

function cdayf() {
//if ((ccy>sccy)|((ccy==sccy)&&(ccm>=sccm)))
if ((ccy!=sccy)|(ccy==sccy))
        return;
else {
        ccy=sccy;
        ccm=sccm;
        cfd=scfd;
     }
}

//-------------------------------------------------------------------------------
function validateDate(fld,ancien,Uid) {
        var RegExPattern = /^(?=\d)(?:(?:(?:(?:(?:0?[13578]|1[02])(\/|-|\.)31)\1|(?:(?:0?[1,3-9]|1[0-2])(\/|-|\.)(?:29|30)\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})|(?:0?2(\/|-|\.)29\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))|(?:(?:0?[1-9])|(?:1[0-2]))(\/|-|\.)(?:0?[1-9]|1\d|2[0-8])\4(?:(?:1[6-9]|[2-9]\d)?\d{2}))($|\ (?=\d)))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\ [AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
        var errorMessage = 'Le seul format valable est le suivant : dd/mm/yyyy\nUne erreur de saisie ou inhérente à la date à été commise\nRefaites une saisie valide en vous aidant du calendier';
        if ((fld.value.match(RegExPattern)) && (fld.value!='')) {
                return fld;
        } else {
                alert(errorMessage);//alert('document.getElementById('+Uid+').innerHTML='+ancien);
                //document.getElementById(Uid).innerHTML=ancien;
                //return; ancien;
                fld.focus();
        }
}