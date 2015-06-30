.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


TYPO3 CMS Extension "page_speed"
================================
This extensions provides an entry in the info module to check the **usability** & **performance**
of the selected page by using the API of `Google Page Speed Insights <https://developers.google.com/speed/pagespeed/insights/>`_.

Every page is automatically checked for the resolution **desktop** & **mobile**.

Screenshot
----------

.. figure:: Resources/Public/Documentation/screenshot.png
		:alt: Screenshot of the backend

Requirements
------------
- TYPO3 CMS 7.2+
- Google Account

Installation & Configuration
----------------------------
The extension just needs to be installed as any other extension.
To make it work, you need a Google account and activate the PageSpeed service.
After successfully activating the PageSpeed service you need to set the key in the configuration in EM and deactivate the demo mode.

Enable PageSpeed Service
^^^^^^^^^^^^^^^^^^^^^^^^
1) Open `console.developers.google.com <https://console.developers.google.com/>`_ and create a new project.
2) Open the project and switch to *APIs and auth* > *APIs* and **Activate the API**.
3) Switch to *Credentials* and click the button **Create new key** for *public API access*.
4) Create either a *Server key* or a *Browser key**.
5) Test the access by using cUrl on the server where you want to use the extension by calling the command ``curl 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?strategy=desktop&url=http://www.bbc.co.uk/&key=AIzaSyA2rQEy7UyQz-9D1-7bnmw1jzSo0FmobYM'``. (If you get a ``Bad request``, just wait a few minutes to get the key deployed on all Google servers)

Preview Domain
^^^^^^^^^^^^^^
If you need to use a different domain in the frontend or no sys domain record is available, you can use the following configuration inside your **PageTsConfig**: ::

	TCEMAIN.previewDomain = localhost/sites/t3/master

Avoid error "require_once(Net/URL2.php)"
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you see an exception about the
file ``Net/URL2.php`` not found, please open the file ``typo3/contrib/vendor/pear/http_request2/HTTP/Request2.php`` and
remove the *require_once* statement in line 24. This bug will be gone soon.

Technical background
--------------------
A little bit technical information for the ones who are interested in:

The API is described in detail at `developers.google.com/speed/docs/insights/v2/reference/pagespeedapi/runpagespeed <https://developers.google.com/speed/docs/insights/v2/reference/pagespeedapi/runpagespeed>`_
and it is very simple to test by just using curl: ::

	# Desktop
	curl 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?strategy=desktop&screenshot=true&url=http://www.typo3.org&key=<your_key>'
	# Mobile
	curl 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?strategy=mobile&screenshot=true&url=http://www.typo3.org&key=<your_key'

Further development
-------------------



Todos
^^^^^

- Record selection
- Check if current page or rootline is restricted
- Check if current page's translation should not be be shown
- Auto cleanup after page cache clearing

