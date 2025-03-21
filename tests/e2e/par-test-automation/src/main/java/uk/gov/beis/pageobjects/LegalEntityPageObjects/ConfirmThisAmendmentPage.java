package uk.gov.beis.pageobjects.LegalEntityPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class ConfirmThisAmendmentPage extends BasePageObject {
	
	@FindBy(id = "edit-confirmation")
	private WebElement confirmationCheckbox;
	
	@FindBy(id = "edit-next")
	private WebElement submitAmendmentBtn;
	
	public ConfirmThisAmendmentPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void deselectConfirmationCheckbox() {
		if(confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}
	
	public void selectConfirmationCheckbox() {
		if(!confirmationCheckbox.isSelected()) {
			confirmationCheckbox.click();
		}
	}
	
	public void selectSubmitAmendmentButton() {
		submitAmendmentBtn.click();
	}
	
	public void goToAmendmentCompletedPage() {
		WebElement element = driver.findElement(By.xpath("//input[@value='Confirm amendment']"));
		executeJavaScript("arguments[0].click();", element);
	}
}
