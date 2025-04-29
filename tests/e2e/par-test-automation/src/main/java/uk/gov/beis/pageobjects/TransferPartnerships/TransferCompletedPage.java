package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class TransferCompletedPage extends BasePageObject {

	@FindBy(xpath = "//a[contains(text(), 'Done')]")
	private WebElement doneBtn;

	public TransferCompletedPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void selectDoneButton() {
        waitForElementToBeClickable(By.xpath("//a[contains(text(), 'Done')]"), 2000);
        doneBtn.click();
        waitForPageLoad();
	}
}
