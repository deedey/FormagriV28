<cfset ApiPath = 'ApiScorm.cfm'>
<cfinclude template="#ApiPath#">
<frameset rows='0,*'>
     <frame src='' name='nothing' frameborder='0' scrolling='auto' resize='yes' />
     <CFSET liens = URLDECODE(#lien#)>
     <frame src="<CFOUTPUT>#liens#</CFOUTPUT>" name='contenu' frameborder='0' scrolling='auto' />
</frameset>

