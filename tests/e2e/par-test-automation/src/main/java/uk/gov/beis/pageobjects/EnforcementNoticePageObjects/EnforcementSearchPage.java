package uk.gov.beis.pageobjects.EnforcementNoticePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class EnforcementSearchPage extends BasePageObject {

	@FindBy(id = "edit-combine")
	private WebElement searchInput;
	
	@FindBy(id = "edit-submit-par-user-enforcements")
	private WebElement searchBtn;
	
	@FindBy(xpath = "//div/p[contains(text(),'Sorry, there are no sent or received notices')]")
	private WebElement noResults;
	
	private String status = "//td[contains(text(),'?')]/preceding-sibling::td[1]";
	private String removeEnfBtn = "//td[contains(text(),'?')]/following-sibling::td[1]/a[contains(text(),'remove enforcement')]";
	
	public EnforcementSearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchForEnforcementNotice(String search) {
		searchInput.clear();
		searchInput.sendKeys(search);
		searchBtn.click();
	}
	
	public ProposedEnforcementPage selectEnforcement() {
		driver.findElement(By.linkText(DataStore.getSavedValue(UsableValues.ENFORCEMENT_TITLE))).click();
		return PageFactory.initElements(driver, ProposedEnforcementPage.class);
	}
	
	public RemoveEnforcementPage removeEnforcement() {
		driver.findElement(By.xpath(removeEnfBtn.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).click();
		return PageFactory.initElements(driver, RemoveEnforcementPage.class);
	}

	public boolean confirmNoReturnedResults() {
		boolean value = false;
		if (driver.findElement(By.xpath("//div/p[contains(text(),'Sorry, there are no sent or received notices')]")).isDisplayed())
		{
			value = true;
		}
		
		return value;
	}
	
	public String getStatus() {
		return driver.findElement(By.xpath(status.replace("?", DataStore.getSavedValue(UsableValues.BUSINESS_NAME)))).getText();
	}
}
