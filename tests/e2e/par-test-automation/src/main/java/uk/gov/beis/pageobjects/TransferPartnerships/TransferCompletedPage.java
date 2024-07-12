package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

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
		doneBtn.click();
	}
}
