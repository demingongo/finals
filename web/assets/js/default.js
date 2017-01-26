function addLink() {
		if (typeof window.getSelection == "undefined") return; //IE8 or earlier...

        //Get the selected text and append the extra info
        var selection = window.getSelection();
		if (("" + selection).length < 50) return;

        var pagelink = '<br/><br/> - See more at: ' + document.location.href,
            copytext = selection + pagelink,
            newdiv = document.createElement('div');

        //hide the newly created container
        newdiv.style.position = 'absolute';
        newdiv.style.left = '-99999px';

        //insert the container, fill it with the extended text, and define the new selection
        document.body.appendChild(newdiv);
        newdiv.innerHTML = copytext;
        selection.selectAllChildren(newdiv);

        window.setTimeout(function () {
            document.body.removeChild(newdiv);
        }, 100);
    }

document.addEventListener('copy', addLink); /* http://jsfiddle.net/jp6nhmxf/ */