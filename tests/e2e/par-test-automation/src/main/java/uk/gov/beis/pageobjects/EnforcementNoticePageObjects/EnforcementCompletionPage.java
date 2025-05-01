package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforcementCompletionPage extends BasePageObject{

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;

	public EnforcementCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneButton() {
        waitForElementToBeClickable(By.xpath("//a[contains(@class,'button')]"), 3000);
        doneBtn.click();
        waitForPageLoad();
	}
}
