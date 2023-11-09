package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class EnforcementDetailsPage extends BasePageObject {
	
	@FindBy(id = "edit-summary")
	private WebElement descriptionBox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String enforcementTypeLocator = "//label[contains(text(),'?')]";
	
	public EnforcementDetailsPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void selectEnforcementType(String type) {
		driver.findElement(By.xpath(enforcementTypeLocator.replace("?", type))).click();
	}
	
	public void enterEnforcementDescription(String description) {
		descriptionBox.clear();
		descriptionBox.sendKeys(description);
	}
	
	public EnforcementActionPage clickContinue() {
		continueBtn.click();
		return PageFactory.initElements(driver, EnforcementActionPage.class);
	}
}
