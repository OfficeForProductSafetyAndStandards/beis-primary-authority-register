package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class RegulatoryFunctionPage extends BasePageObject {

	public RegulatoryFunctionPage() throws ClassNotFoundException, IOException {
		super();
	}

	private String regFunction = "//div/label[contains(text(),'?')]/preceding-sibling::input";
	
	@FindBy(id = "edit-partnership-cover-default")
	private WebElement normalOrSequencedRadio;
	
	@FindBy(id = "edit-partnership-cover-bespoke")
	private WebElement bespokeRadio;
	
	@FindBy(id = "edit-regulatory-functions-4")	// Note: May have more check boxes in the future.
	private WebElement bespokeCheckbox;

	@FindBy(id = "edit-next")
	private WebElement continueBtn;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AuthorityConfirmationPage selectRegFunction(String reg) {
		List<WebElement> boxes = driver.findElements(By.xpath("//div/label/preceding-sibling::input"));
		
		// clear up boxes first
		for (WebElement bx : boxes) {
			if (bx.isSelected()){
				bx.click();
			}
		}
		
		driver.findElement(By.xpath(regFunction.replace("?", reg))).click();
		
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
	}
	
	public AuthorityConfirmationPage editRegFunction(String reg) {
		List<WebElement> boxes = driver.findElements(By.xpath("//div/label/preceding-sibling::input"));
		
		// clear up boxes first
		for (WebElement bx : boxes) {
			if (bx.isSelected()){
				bx.click();
			}
		}
		
		driver.findElement(By.xpath(regFunction.replace("?", reg))).click();
		
		saveBtn.click();
		return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
	}

	public PartnershipApprovalPage proceed() {
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipApprovalPage.class);
	}
	
	public PartnershipConfirmationPage updateRegFunction() {
		if(normalOrSequencedRadio.isSelected()) {
			bespokeRadio.click();
			
			if(!bespokeCheckbox.isSelected()) {
				bespokeCheckbox.click();
			}
		}
		else if(bespokeRadio.isSelected()) {
			normalOrSequencedRadio.click();
		}
		
		DataStore.saveValue(UsableValues.PARTNERSHIP_REGFUNC, "Cookie control"); // Would be better to use Bespoke and Normal or Sequenced as the value.
		
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
	
	public PartnershipConfirmationPage clickSave() {
		saveBtn.click();
		return PageFactory.initElements(driver, PartnershipConfirmationPage.class);
	}
}
