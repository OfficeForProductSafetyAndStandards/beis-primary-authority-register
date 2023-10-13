package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmThisTranferPage extends BasePageObject {
	
	@FindBy(id = "edit-confirmation")
	private WebElement confirmationCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement transferBtn;
	
	public ConfirmThisTranferPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public TransferCompletedPage confirmPartnershipTransfer() {

		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
		
		transferBtn.click();
		
		return PageFactory.initElements(driver, TransferCompletedPage.class);
	}
}
