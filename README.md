# Storio

Storio is a self-hosted file manager with multi-user capabilities. It includes permissions on a per-user basis with regards to uploading files and a maximum storage space per user. Storio does not need any database to function.

## Features

* Admin user(s) to manage sub-user permissions
* Ability to set default storage or allocate per-user
* Enable/disable user registration
* JavaScript multiple upload with progress
* Folder/sub-folder creation
* Global file search
* Image & video preview
* Share links with optional download page
* Site/user permissions stored in JSON
* Fully responsive
* Installer with storage folder selection
* Thumbnails for both image and video files (composer/ffmpeg required for video thumbnails)
* CLI interface (WIP)

## Demo

A demo installation can be seen at https://satapod.com

## Installation

Installation is simply done via a git clone

```bash
git clone https://github.com/ialexpw/Storio.git
```
Then direct your browser to the install directory and follow the steps from there. Web root is within the public/ folder.

## Screenshots

### Login screen
![image](https://user-images.githubusercontent.com/7994724/174810368-ab1a3a19-3043-4871-a3aa-d3b21068a1da.png)


### Admin screenshots

Dashboard
![image](https://user-images.githubusercontent.com/7994724/174809288-ea4ddcaa-9f0d-4483-8cf6-6875bc0e1ff7.png)

User management
![image](https://user-images.githubusercontent.com/7994724/174809495-6c91f6d0-bf7b-480a-97aa-4afbbc377698.png)

System settings
![image](https://user-images.githubusercontent.com/7994724/174809668-7dfeaccf-78cf-4777-8c65-505817f65765.png)


### User screenshots

Dashboard
![image](https://user-images.githubusercontent.com/7994724/174809823-737e9067-8356-49bc-95a5-1ac278535cee.png)

File listing
![image](https://user-images.githubusercontent.com/7994724/206696498-bb3b8fac-1173-4d08-9369-a2ef8730a1a6.png)

User settings
![image](https://user-images.githubusercontent.com/7994724/174810028-7acb7140-5ca7-4a05-a76b-a47b733fc2b1.png)

File uploads
![image](https://user-images.githubusercontent.com/7994724/174810122-a99ff653-8512-4513-856d-a225f563526c.png)

Folder creation
![image](https://user-images.githubusercontent.com/7994724/174810175-1162216f-538e-4109-8908-81229de22c91.png)


### Sharing

Single file
![image](https://user-images.githubusercontent.com/7994724/174810556-209d1c2f-e810-4c89-9ebd-ec7da8bdc34d.png)

Multiple files
![image](https://user-images.githubusercontent.com/7994724/206472753-f897169f-5377-44e1-b2c6-814bbbc2002d.png)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)
