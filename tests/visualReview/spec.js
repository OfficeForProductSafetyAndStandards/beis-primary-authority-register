var vr = browser.params.visualreview;

describe('angularjs homepage', function() {

  beforeAll(function () {
    browser.manage().window().setSize(1024, 768);
  });
  it('should open the homepage', function() {
    browser.get('https://tranquil-reef-9656.herokuapp.com');
    browser.sleep(1000);
    vr.takeScreenshot('AngularJS-homepage');
  });
});
