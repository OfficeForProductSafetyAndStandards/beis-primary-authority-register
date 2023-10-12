package uk.gov.beis.pageobjects.TransferPartnerships;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.pageobjects.EnterTheDatePage;

public class PartnershipMigrationSelectionPage extends BasePageObject {
	
	@FindBy(id = "edit-authority")
	private WebElement authorityTextfield;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String partnershipLocator = "//label[contains(text(), '?')]/preceding-sibling::input";
	
	public PartnershipMigrationSelectionPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public EnterTheDatePage selectPartnership(String businessName) {

		WebElement checkbox = driver.findElement(By.xpath(partnershipLocator.replace("?", businessName)));
		
		if(!checkbox.isSelected()) {
			checkbox.click();
		}
		
		continueBtn.click();
		
		return PageFactory.initElements(driver, EnterTheDatePage.class);
	}
}
