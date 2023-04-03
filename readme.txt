// readme.txt
// 20230327 1305
// KMB

Long Term Tracker is a Data Driven Web App which tracks progress for a given fitness goal 
and compares user performance to previously inputted data as well as to other users of the 
same fitness pursuit. It calculates trends using regression analysis, and provides 
charts to illustrate progress.

All of the code is hand rolled, and no libraries or frameworks are used. The code is 
written in PHP, and employs some limited JavaScript and JSON where that makes the implementation 
easier. The entire product is supported by a mySQL database hosted on a web server. All of 
these components are required in order to build the fully functional product.

The consumer facing portal, currently called windsor, is supported by a staff facing portal 
called balmoral. They operate in tandem and allow staff (of a commercial operation) to oversee 
the usage of the consumer app by the consumers, and usage of the staff app by other staff. The 
consumer facing portal is the "Long Term Tracker". 

This is a public version on GitHub, and contains the word REDACTED in place of sensitive data.

The database has dummy data exists for many dates from 20230101 to 20230228 - ranges should be 
10 days or more, else there may be insufficient data to build a chart.

Selecting 20230101 to 20230116 illustrates how this works. Occasionally, other dates push 
the data off the scale. 
