# api-hud-goggle

This project will contain Api for the Hud Goggles, so a server can be setup to host online services:  
login  
signup  
trips  
buddies  
...  

## End points

### Login Controller

/login - Provides a entry point for the users connect to get a token  
/token - To get a token  
/signup - To sign up  

### Budies Controller

/userss - Query friends  
/friend - accept, invite, reject, remove  
/meefriendsslocationss - get locations of friends  
/meelocationss - update my locations  

### Trip Controller

/meetripss - get my trips  
/downloadgpx - download gpx for a trip  
/metripsupdate - update my trips  

### Package Controller

/packages - get available packages for update  
/packagesupdate - Updates the packages to be downloaded by user (it is used by the web)  
/packagedownload - Download large packages and apps from the app store  
/mepackagessupdate - Update all the packages a user have  
/mepackagessupdatebin - update binary package to server to check  


