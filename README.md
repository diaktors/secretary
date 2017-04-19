Secretary [![Build Status](https://travis-ci.org/wesrc/secretary.png)](https://travis-ci.org/wesrc/secretary)
=========
A secure place for shared infos.

![mockup](https://www.wesrc.com/img/secretary/secretary_dashboard.png)

Idea behind
-----------
This app tries to solve a common problem: Saving sensitive information inside a db you don't own and (in most cases) will be saved unencrypted. We are all using email, IM, chats, online apps (even github) to share passwords, credentials and other stuff. And we all know while doing it, that this is wrong. Definitely! We never ever want people, who do not have the given permission by us, to *read* or *use* that information.

And while laughing at people sending fax messages: What is *your* preferred way of sharing this kind of information with other people. Really think about this for one minute. And if you got a good solution for this problem, please let me know.

I tried encrypted emails, with GNUPG and smime. Do all the people you want to share sensitive information with have a smime cert, a gnupg key? No, they don't. And I won't be the person training all these people out there to create smime certs for money or getting them into GNUPG and the theory behind.

We have Dropbox, iCloud and many other cloud services where we can share files. Most of them tell you, that content is encrypted, but some tell you, that they need to have a way, to get to your data.

And if you try to sync an encrypted DiskImage with Dropbox with other people, you definitely will have fun with sync conflicts.

So, I want to share sensitive information with other people, business partners, customers. And I have not found any good solution for this basic problem. Talked to my partner about this with the example of server passwords and he was telling me: 

> You don't want to have to work with passwords. In best cases you have a key, and everything you need to do can be done with this key.

But four weeks later we got that problem again and were sending passwords by email.

> But there must be a fine way of doing this

Many Thanks to my partner Leander, he was definitely getting me into the right direction. You want to have a key.

`Secretary` is made for making it easy to share sensitive information in a secure way. Inside groups, with a key, but in a way I could tell my father how to do this. It's not as comfortable as having plaintext around. You need to get permission again and again. But perhaps better and even slightly more secure than fax?

Open Source & Help needed
-------------------------
If you don't want to rely on trusting some cloud app - Please consider to take a look. This code is open source and license is MIT-License (see MIT-LICENSE.txt for more info).

So you can install this on you own server, you have your data in your hands (but encrypted!). You will need SSL for using this app. (YOU DONT WANT TO RUN THIS WITH NORMAL HTTP)

Help with getting this app even better is much appreciated. There are many places where things could be done better: testing, more features, more elegant encryption handling (as far as secure possible (Browser PlugIns for key insertion?)). Would love seeing help.

All security experts out there: I'm not one of you. I make errors, many. But I'm ready to learn from feedback. And I would love to have experienced users to look at the code behind. 

Installation
------------
see docs/Installation.md


API / Apigility
---------------
see docs/Apigility.md

---

Have fun and feel welcome to give feedback,  
Thanks for reading!
