
<div id="top"></div>

<div align="center">

<img src="https://svg-rewriter.sachinraja.workers.dev/?url=https%3A%2F%2Fcdn.jsdelivr.net%2Fnpm%2F%40mdi%2Fsvg%406.7.96%2Fsvg%2Fgoogle-my-business.svg&fill=%234285F4&width=200px&height=200px" style="width:200px;"/>

<h3 align="center">PHP Client to GMB API</h3>

<p align="center">
    PHP Example of connecting to the Google My Business API
</p>
</div>


##  1. <a name='TableofContents'></a>Table of Contents



* 1. [Table of Contents](#TableofContents)
* 2. [About The Project](#AboutTheProject)
	* 2.1. [Built With](#BuiltWith)
* 3. [Getting Started](#GettingStarted)
	* 3.1. [Installation](#Installation)
* 4. [Usage](#Usage)
	* 4.1. [Steps before running](#Stepsbeforerunning)
	* 4.2. [Google My Business](#GoogleMyBusiness)
	* 4.3. [OAUTH Keys](#OAUTHKeys)
	* 4.4. [The Examples](#TheExamples)
* 5. [Contributing](#Contributing)
* 6. [License](#License)
* 7. [Contact](#Contact)
* 8. [Changelog](#Changelog)



##  2. <a name='AboutTheProject'></a>About The Project

This is just a a simple repository of a few of the different methods you can use on the GMB API.

<p align="right">(<a href="#top">back to top</a>)</p>



###  2.1. <a name='BuiltWith'></a>Built With

This project was built with the following frameworks, technologies and software.

* [PHP](https://php.net/)
* [Composer](https://getcomposer.org/)

<p align="right">(<a href="#top">back to top</a>)</p>





##  3. <a name='GettingStarted'></a>Getting Started


###  3.1. <a name='Installation'></a>Installation

1. Clone the repo
    ```sh
    git clone https://github.com/IORoot/examples__google-my-business
    ```


<p align="right">(<a href="#top">back to top</a>)</p>



##  4. <a name='Usage'></a>Usage


###  4.1. <a name='Stepsbeforerunning'></a>Steps before running

1. Make sure you do a `composer install` or `composer update` to download all vendor packages. 
1. Also, make sure the `client_secret.json` file is placed in the root of this folder (See below). There is a .gitignore on the file `client_secret.json`, so you don't commit it to a repo by accident.
1. You can run this with a simple PHP built-in webserver or any other. 
```bash
php -S localhost:8000
```


###  4.2. <a name='GoogleMyBusiness'></a>Google My Business

Is one of the only client-libraries that is NOT part of the standard `google/apiclient-services`, so I've included the "bronhy/google-my-business-php-client" repo to automatically install it.

###  4.3. <a name='OAUTHKeys'></a>OAUTH Keys

Head over the the google console https://console.cloud.google.com/ and create a project with the following APIs:

- Google My Business

The google my business API is a private one and you need to request access from google for it.

Next:
- Create an OAUTH 2.0 client ID.
- In the Authorised redirect URIs include:
    - http://localhost:8000
    - http://localhost:8000/index.php
- Download the JSON file and save it as `client_secret.json` and place it into the root of this folder.

###  4.4. <a name='TheExamples'></a>The Examples

All examples are in the `examples` folder. They are all self-contained, so each file should contain everything it needs. Pull out the one you want and place it into the root folder and rename it as`index.php`. Run your webserver and view!



<p align="right">(<a href="#top">back to top</a>)</p>



##  5. <a name='Contributing'></a>Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue.
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>



##  6. <a name='License'></a>License

Distributed under the MIT License.

MIT License

Copyright (c) 2022 Andy Pearson

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

<p align="right">(<a href="#top">back to top</a>)</p>



##  7. <a name='Contact'></a>Contact

Project Link: [https://github.com/IORoot/...](https://github.com/IORoot/...)

<p align="right">(<a href="#top">back to top</a>)</p>



##  8. <a name='Changelog'></a>Changelog

v1.0.0 - First version.
