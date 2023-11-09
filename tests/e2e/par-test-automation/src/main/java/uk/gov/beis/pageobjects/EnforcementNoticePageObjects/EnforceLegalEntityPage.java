package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforceLegalEntityPage extends BasePageObject {
	
	@FindBy(id = "edit-alternative-legal-entity")
	private WebElement legalEntityNameField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	//private String legEnt = "//div/label[contains(text(),'?')]";
	
	public EnforceLegalEntityPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterLegalEntityName(String name) {
		legalEntityNameField.clear();
		legalEntityNameField.sendKeys(name);
	}
	
	//public EnforcementLegalEntityPage selectLegalEntity(String ent) {
	//	driver.findElement(By.xpath(legEnt.replace("?", ent))).click();
	//	return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	//}
	
	//public EnforcementLegalEntityPage enterEntity(String entity) {
	//	legalEntityNameField.sendKeys(entity);
	//	return PageFactory.initElements(driver, EnforcementLegalEntityPage.class);
	//}

	public EnforcementDetailsPage clickContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementDetailsPage.class);
	}
}
