package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class RegulatoryFunctionPage extends BasePageObject {

	public RegulatoryFunctionPage() throws ClassNotFoundException, IOException {
		super();
	}

	private String regFunction = "//div/label[contains(text(),'?')]/preceding-sibling::input";

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	public AuthorityConfirmationPage selectRegFunction(String reg) {
		WebElement box = driver.findElement(By.xpath(regFunction.replace("?", reg)));
		if (!box.isSelected())
			box.click();
		continueBtn.click();
		return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
	}

	public PartnershipApprovalPage proceed() {
		driver.findElement(By.xpath("//input[contains(@value,'Continue')]")).click();
		return PageFactory.initElements(driver, PartnershipApprovalPage.class);
	}
}
