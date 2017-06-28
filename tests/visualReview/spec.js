var vr = browser.params.visualreview;

describe('angularjs homepage', function() {

  beforeAll(function () {
    browser.manage().window().setSize(1024, 768);
  });

  it('should open the homepage', function() {
    browser.ignoreSynchronization = true;
    browser.get('https://govuk-elements.herokuapp.com/layout/');
    browser.sleep(1000);
    vr.takeScreenshot('AngularJS-homepage');
  });

  // it('should to go the docs', function () {
  //   element(by.css('[href="api/ng/function/angular.injector"]')).click()
  //   vr.takeScreenshot('Injector');
  // });
  //
  // it('should edit the source', function () {
  //   element(by.css('[href="guide/di"]')).click()
  //   vr.takeScreenshot('Guide');
  // });
});
