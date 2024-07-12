package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class MemberListCountPage extends BasePageObject {
	
	@FindBy(id = "edit-number-members")
	private WebElement memberNumberTextfield;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	public MemberListCountPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectMemberListType(String count) {
		memberNumberTextfield.clear();
		memberNumberTextfield.sendKeys(count);
	}
	
	public void clickContinueButton() {
		continueBtn.click();
	}
}
