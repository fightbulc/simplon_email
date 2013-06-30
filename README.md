<pre>
     _                 _                                    _ _
 ___(_)_ __ ___  _ __ | | ___  _ __     ___ _ __ ___   __ _(_) |
/ __| | '_ ` _ \| '_ \| |/ _ \| '_ \   / _ \ '_ ` _ \ / _` | | |
\__ \ | | | | | | |_) | | (_) | | | | |  __/ | | | | | (_| | | |
|___/_|_| |_| |_| .__/|_|\___/|_| |_|  \___|_| |_| |_|\__,_|_|_|
                |_|                                             
</pre>

# Simplon Email

This library helps to build emails by utilising [SwiftMailer](https://github.com/swiftmailer/swiftmailer).

It enables the developer to send an email as plain/html version with automatic image detection/inline injection. It is possible to set the plain/html body directly or to via templates in combination with variable injection.

### Setup

Since its a composer package all you need to to do is require it within your composer package definitions and install/update it:

```json
{
     "require": {
        "simplon/email": "0.0.*"
     }
}
```

If you dont know what ```composer``` is you should have a look at [Composer's Webpage](http://getcomposer.org/doc/00-intro.md).

### Send a basic email

#### EmailConfigVo

This class holds our config data which will be injected through the Email class constructor. There is only one thing which needs to be set to get it running: transport instance for sending our email.

```php
// php's internal mail
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_MailTransport::newInstance());

// or via smtp transport
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_SmtpTransport::newInstance('localhost', 25));
```

In case you want to parse for images within your html body you should also provide a root path to your template directory. We assume that templates and images are located under the same template directory.

```php
// php's internal mail and root path templates
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_MailTransport::newInstance())
   ->setPathRootTemplates(__DIR__ . '/templates');
```

```html
<h1>Hello world!</h1>
{{image:folder01/logo.png}}
```

The above configuration would find the defined image within ```__DIR__ . '/templates/folder01/logo.png'```.

#### Defining an email

We use a builder pattern to define our email. It's really simple and doesn't demand any further insight:

```php
$emailVo = (new \Simplon\Email\Vo\EmailVo())
   ->setFrom('name@mailer.from', 'FromName')
   ->setTo('name@receiver.to')
   ->setSubject('Basic email')
   ->setBodyPlain('Hey man! Hope this email finds you well!')
   ->setBodyHtml('<h1>Hey man!</h1> Hope this email finds you well!');
```

That's all what it takes. Now we can take our defined email in ```$emailVo``` and pass it on to our Email class.

#### Complete example

See the following complete example of sending a basic email.

```php
require __DIR__ . '/../vendor/autoload.php';

// ##########################################

// set config
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_MailTransport::newInstance());

// ------------------------------------------

// set vo
$emailVo = (new \Simplon\Email\Vo\EmailVo())
   ->setFrom('name@mailer.from', 'FromName')
   ->setTo('name@receiver.to')
   ->setSubject('Basic email')
   ->setBodyPlain('Hey man! Hope this email finds you well!')
   ->setBodyHtml('<h1>Hey man!</h1> Hope this email finds you well!');

// ------------------------------------------

// send email
$response = (new \Simplon\Email\Email($emailConfigVo))->sendEmail($emailVo);

// BOOL to indicate if all went fine
var_dump($response);
```

### Send an email based on templates

#### EmailConfigVo

Same as written above applies here. Only difference you always need to define the root template path:

```php
// php's internal mail and root path templates
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_MailTransport::newInstance())
   ->setPathRootTemplates(__DIR__ . '/templates');
```

#### Defining a template email

Again, same as above but the template fields as well as some content variables.

#### Plain email

```php
$contentVars = [
   'name' => 'Tino',
   'age'  => 32,
   'date' => date('r'),
];

$emailTemplateVo = (new \Simplon\Email\Vo\EmailTemplateVo())
   ->setPathRootTemplates($emailConfigVo->getPathRootTemplates())
   ->setFrom('name@mailer.from', 'FromName')
   ->setTo('name@receiver.to')
   ->setSubject('Herro!')
   ->setPathTemplatePlainFile('tmpl01/plain_template.txt')
   ->setPathContentPlainFile('tmpl01/plain_content.txt')
   ->setContentVariables($contentVars);
```

#### Plain/Html email

```php
$contentVars = [
   'name' => 'Tino',
   'age'  => 32,
   'date' => date('r'),
];

$emailTemplateVo = (new \Simplon\Email\Vo\EmailTemplateVo())
   ->setPathRootTemplates($emailConfigVo->getPathRootTemplates())
   ->setFrom('name@mailer.from', 'FromName')
   ->setTo('name@receiver.to')
   ->setSubject('Herro!')
   ->setPathTemplatePlainFile('tmpl01/plain_template.txt')
   ->setPathContentPlainFile('tmpl01/plain_content.txt')
   ->setPathTemplateHtmlFile('tmpl01/html_template.html')
   ->setPathContentHtmlFile('tmpl01/html_content.html')
   ->setContentVariables($contentVars);
```

The only thing left now are the templates. See the following section.

#### Templates

All templates need to be located below the defined ```setPathRootTemplates```. Templates are defined by two types:

##### A structural template (tmpl01/plain_template.txt)

```text
HEADER

###################

{{content}}

###################

FOOTER

-------------------

Yes we can inject
content vars here too:

{{date}}
```

##### A content template (tmpl01/plain_content.txt)

```text
This is my content template
with content variables such as
{{name}} and {{age}}.
```

##### Final email body

```text
HEADER

###################

This is my content template
with content variables such as
Tino and 32.

###################

FOOTER

-------------------

Yes we can inject
content vars here too:

Sun, 30 Jun 2013 10:44:20 +0000
```

#### Complete example

See the following complete example of sending an email with templates.

```php
require __DIR__ . '/../vendor/autoload.php';

// ##########################################

// set config
$emailConfigVo = (new \Simplon\Email\Vo\EmailConfigVo())
   ->setTransportInstance(Swift_SmtpTransport::newInstance())
   ->setPathRootTemplates(__DIR__ . '/templates');

// ------------------------------------------

// set content vars
$contentVars = [
   'name' => 'Tino',
   'age'  => 32,
   'date' => date('r'),
];

// set vo
$emailTemplateVo = (new \Simplon\Email\Vo\EmailTemplateVo())
   ->setPathRootTemplates($emailConfigVo->getPathRootTemplates())
   ->setFrom('name@mailer.from', 'FromName')
   ->setTo('name@receiver.to')
   ->setSubject('Herro!')
   ->setPathTemplatePlainFile('tmpl01/plain_template.txt')
   ->setPathContentPlainFile('tmpl01/plain_content.txt')
   ->setContentVariables($contentVars);

// ------------------------------------------

// send email
$response = (new \Simplon\Email\Email($emailConfigVo))->sendEmailByTemplate($emailTemplateVo);

// BOOL to indicate if all went fine
var_dump($response);
```

# Anything else?
Still in doubt how to use this library? Have a look at the ```test``` folder.

# License
Simplon\Email is freely distributable under the terms of the MIT license.

Copyright (c) 2013 Tino Ehrich ([opensource@efides.com](mailto:opensource@efides.com))

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
