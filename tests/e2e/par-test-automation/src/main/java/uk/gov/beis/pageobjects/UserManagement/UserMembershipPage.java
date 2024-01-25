package uk.gov.beis.pageobjects.UserManagement;

import java.io.IOException;
import java.time.Duration;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import uk.gov.beis.pageobjects.BasePageObject;

public class UserMembershipPage extends BasePageObject {
	
	@FindBy(id = "edit-par-data-authority-id")
	private WebElement authorityTextField;
	
	@FindBy(id = "edit-par-data-organisation-id")
	private WebElement organisationTextField;
	
	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	private String authorityRadioLocator = "//label[contains(normalize-space(), '?')]/preceding-sibling::input";
	
	public UserMembershipPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void chooseAuthorityMembership(String authorityName) {
		if(authorityTextField != null) {
			authorityTextField.clear();
			authorityTextField.sendKeys(authorityName);
			
			WebElement widget = driver.findElement(By.id("ui-id-1"));
			
			WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
			
			wait.until(ExpectedConditions.visibilityOf(widget));
			
			if(widget.isDisplayed()) {
				widget.click();
			}
		}
		else {
			WebElement authorityRadio = driver.findElement(By.xpath(authorityRadioLocator.replace("?", authorityName)));
			
			if(authorityRadio != null) {
				authorityRadio.click();
			}
		}
	}
	
	public void chooseOrganisationMembership(String organisationName) {
		if(organisationTextField != null) {
			organisationTextField.clear();
			organisationTextField.sendKeys(organisationName);
			
			WebElement widget = driver.findElement(By.id("ui-id-2"));
			
			WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
			
			wait.until(ExpectedConditions.visibilityOf(widget));
			
			if(widget.isDisplayed()) {
				widget.click();
			}
		}
	}
	
	public AddMembershipConfirmationPage clickContinueButton() {
		continueBtn.click();
		return PageFactory.initElements(driver, AddMembershipConfirmationPage.class);
	}
}
