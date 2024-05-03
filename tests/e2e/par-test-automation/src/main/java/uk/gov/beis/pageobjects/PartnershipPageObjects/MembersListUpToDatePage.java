package uk.gov.beis.pageobjects.PartnershipPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.pageobjects.BasePageObject;

public class MembersListUpToDatePage extends BasePageObject {
	
	@FindBy(id = "edit-confirm-yes")
	private WebElement yesRadial;
	
	@FindBy(id = "edit-confirm-no")
	private WebElement noRadial;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public MembersListUpToDatePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectYesRadial() {
		yesRadial.click();
	}
	
	public void selectNoRadial() {
		noRadial.click();
	}
	
	public void clicksaveButton() {
		saveBtn.click();
	}
}
