package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

public class PublicRegistrySearchPage extends BasePageObject {
	
	@FindBy(id = "edit-keywords")
	private WebElement searchInput;
	
	@FindBy(id = "edit-submit-primary-authority-register")
	private WebElement searchBtn;
	
	@FindBy(xpath = "//td[@class='views-field views-field-organisation-name']")
	private WebElement partnershipTableFirstElement;
	
	public PublicRegistrySearchPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void searchForPartnership(String partnership) {
		searchInput.sendKeys(partnership);
	}
	
	public void clickSearchButton() {
		searchBtn.click();
	}
	
	public Boolean partnershipContains(String name) {
		return partnershipTableFirstElement.getText().contains(name);
	}
}
