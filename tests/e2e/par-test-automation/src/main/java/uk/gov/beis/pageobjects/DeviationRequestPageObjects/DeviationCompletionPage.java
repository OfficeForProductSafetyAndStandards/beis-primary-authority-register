package uk.gov.beis.pageobjects.DeviationRequestPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class DeviationCompletionPage extends BasePageObject{

	@FindBy(xpath = "//a[contains(@class,'button')]")
	private WebElement doneBtn;

	public DeviationCompletionPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void clickDoneButton() {
        waitForElementToBeClickable(By.xpath("//a[contains(@class,'button')]"), 3000);
        doneBtn.click();
        waitForPageLoad();
	}
}
