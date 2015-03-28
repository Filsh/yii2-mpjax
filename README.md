# yii2-mpjax

Multi pjax + backbone.js implementation

# Installation

The preferred way to install this extension is through composer.

Either run

php composer.phar require --prefer-dist filsh/yii2-mpjax "*"

or add

"filsh/yii2-mpjax": "*"

to the require section of your composer.json

# Usage

```php
$this->beginMpjax('header');
    echo '<div>header</div>';
$this->endMpjax();

$this->beginMpjax('body');
    echo '<div>body</div>';
$this->endMpjax();
```

```javascript
Backbone.Mpjax = {
    Router: Backbone.Router.extend({
        navigate: function (fragment, options) {
            this.trigger('mpjax:start', fragment);
            Backbone.Router.prototype.navigate.apply(this, [fragment, options]);

            var self = this;
            $.ajax({
                url: fragment,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-PJAX', 'true');
                    xhr.setRequestHeader('X-PJAX-Container-0', 'header');
                    xhr.setRequestHeader('X-PJAX-Container-1', 'body');
                },
                success: function (data) {
                    //  Object { header="<div>header</div>",  body="<div>body</div>"}
                    self.trigger('mpjax:success');
                },
                error: function () {
                    self.trigger('mpjax:error');
                }
            });
        }
    })
};

var Router = Backbone.Mpjax.Router.extend({
  routes: {
    "test": "test"
  },

  test: function(){
    console.log("Test!");
  }
});
```
