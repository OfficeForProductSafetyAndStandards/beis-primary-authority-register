package uk.gov.beis.pageobjects;

import java.io.IOException;
import java.util.List;

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
		List<WebElement> boxes = driver.findElements(By.xpath("//div/label/preceding-sibling::input"));
		// clear up boxes first
		for (WebElement bx : boxes) {
			if (bx.isSelected())
				bx.click();
		}
		driver.findElement(By.xpath(regFunction.replace("?", reg))).click();
		try {
			driver.findElement(By.id("edit-next")).click();
			return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		} catch (Exception e) {
			driver.findElement(By.id("edit-save")).click();
			return PageFactory.initElements(driver, AuthorityConfirmationPage.class);
		}
	}

	public PartnershipApprovalPage proceed() {
		driver.findElement(By.xpath("//input[contains(@value,'Continue')]")).click();
		return PageFactory.initElements(driver, PartnershipApprovalPage.class);
	}
}
