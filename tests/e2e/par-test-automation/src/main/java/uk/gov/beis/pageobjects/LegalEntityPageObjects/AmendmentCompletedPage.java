package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class AmendmentCompletedPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(normalize-space(), 'Done')]")
	private WebElement doneBtn;

	public AmendmentCompletedPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneButton() {
        waitForElementToBeClickable(By.xpath("//a[contains(normalize-space(), 'Done')]"), 3000);
        doneBtn.click();
        waitForPageLoad();
	}
}
