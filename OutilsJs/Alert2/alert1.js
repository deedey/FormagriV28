/******************************************
        JavaScript Alert 2
        Class to provide a very customizable alert box
        Author: Tom Coote
        Date: 15 Feb 2007
        Web Site: tomcoote.co.uk
********************************************/

var downY = 0;
var downX = 0;
var bMouseCoOrds = false;

// Try and get coordinates to place the box into the center of the window
// but if not try for mouse coordinates from the given event.
function GetCoords(e)
{
        if (self.innerWidth)
        {
                downX = self.innerWidth / 2;
                downY = self.innerHeight / 2;
        }
        else if (document.documentElement && document.documentElement.clientWidth)
        {
                downX = document.documentElement.clientWidth / 2;
                downY = document.documentElement.clientHeight / 2;
        }
        else if (document.body)
        {
                downX = document.body.clientWidth / 2;
                downY = document.body.clientHeight / 2;
        }
        else
        {
                // This will store the coordinates of a mouse down event
                if (!e) { // IE browsers
                        downX = window.event.x
                        downY = window.event.y
                }
                else {   // Non IE browsers
                        downX = e.clientX
                        downY = e.clientY
                }

                bMouseCoOrds = true;
        }
}

// Create the alert box with the values given.
//
// <param name="event"> The event from which linked to this function eg this.event </param>
// <param name="sTitle"> The title for the alert box </param>
// <param name="sMessage"> The message for the alert box </param>
// <param name="sMessageIcon"> The icon for the message, blank for none</param>
// <param name="aButtons"> An array of button values </param>
// <param name="aFunctions"> An array of button onClick events </param>
// <param name="iWidth"> The width of the alert bo x</param>
// <param name="sCloseIcon"> Path to top right exit icon for the alert box </param>
function ShowAlert2(event, sTitle, sMessage, sMessageIcon, aButtons, aFunctions, iWidth, sCloseIcon)
{
        // Check values
        if (sTitle == '')
                sTitle = "&nbsp;";

        if (sMessage == '')
                sMessage = "&nbsp;";

        if (sMessageIcon != '')
                sMessageIcon = "<img src='" + sMessageIcon + "' width='32' height='32' />";

        if (sCloseIcon != '')
                sCloseIcon = "<img src='" + sCloseIcon + "' height='18' width='18' />";

        if (aButtons.length != aFunctions.length)
                return;

        if (iWidth == '' || iWidth == 0)
                iWidth = 300;

        // Get position for box
        GetCoords(event);

        // Create HTML elements
        alert2 = document.createElement('div');
        alert2.setAttribute ('id' , 'alertBox')
        document.body.appendChild(alert2)
        //alert (event);
        // Create style and set position
        alert2Style = document.getElementById('alertBox').style;

        alert2Style.cssText = "position:fixed;_position:absolute;"; // To support IE6 too.

        if (bMouseCoOrds)
        {
                alert2Style.top = downY + 'px';
                alert2Style.left = downX + 'px';
        }
        else
        {
                alert2Style.top = downY - 50 + 'px';
                alert2Style.left = downX - (iWidth / 2) + 'px';
        }

        alert2Style.width = iWidth + 'px';

        // Input the alert box HTML with custom text, buttons etc
        var sHTMLButtons = '';
        for (i = 0; i < aButtons.length; i++)
        {
                sHTMLButtons += "<input type='button' value='" + aButtons[i] + "' onClick='" + aFunctions[i] + "' >";
                //alert('la fonction = '+aFunctions[i]);
        }

        var sHTML = "<table cellspacing='0' cellpadding='0' border='0' width='100%' id='TitleBar'><tr valign='middle'><td class='titlebar' style='color:#ffffff;font-size:14px;text-align:center;'>" + sTitle + "</td><td width='22' height='22' style='padding-right:2px;' class='titlebar'><a href='JavaScript:HideAlert2(2)' title='Fermer'>"+ sCloseIcon + "</a></td></tr></table>"
                          + "<table cellspacing='0' cellpadding='0' border='0' width='100%'><tr><td id='MessageIcon'>" + sMessageIcon + "</td><td id='Message'>" + sMessage + "</td></tr><tr><td align='right' colspan='2' ><br>" + sHTMLButtons + "</td></tr></table>";

        document.getElementById('alertBox').innerHTML = sHTML;
}

// Use this as the function to hide the alert box
function HideAlert2(mavar)
{
//alert("  mavar="+mavar );
      document.body.removeChild(document.getElementById("alertBox"));
      if (mavar == 1)
           return mavar;
}