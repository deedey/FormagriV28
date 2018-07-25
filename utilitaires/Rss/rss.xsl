<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html"/>
<xsl:template match="rss/channel">
<html>
<head>
        <title><xsl:value-of select="title"/></title>
</head>
<body>
        <div style="margin: 20px">
                <title><xsl:value-of select="title"/></title>
                <a href="{link}" style="font-family: arial; font-size: 16px;font-decoration: none;font-weight:bold; color: #399; margin: 8px"><xsl:value-of select="title"/></a>
        </div>
        <div style="font-family: arial; font-size: 14px;font-decoration: none; color: #399; margin: 10px">
                <description><xsl:value-of select="description"/></description>
        </div>
        <xsl:for-each select="item">
           <div style=" margin-left: 10px; border: 1px dashed #399; margin-bottom: 3px; width: 800px;">
                <div>
                        <span style="font-family: arial; font-size: 14px; color: #39c;">.:: <xsl:value-of select="title"/></span>
                        <span style="font-family: arial; font-size: 12px; color: #000;"> (fait le <xsl:value-of select="pubDate"/></span>
                        <span style="font-family: arial; font-size: 12px; color: #000;">  par <xsl:value-of select="author"/>)</span>
                </div>
                <div style="font-family: arial; font-size: 12px; color: #000; margin-left: 20px;background: #EFEFEF;"> <xsl:value-of select="description" disable-output-escaping="yes" /></div>
           </div>
        </xsl:for-each>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
