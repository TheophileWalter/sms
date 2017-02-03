# Simple Message Signature
https://walter.tw/sms
This website allow you to use RSA based signatures for your messages very simply.  
You just need to generate a key pair and sign a message in one click!  
  
  
## Signature generation
The signatures are generated with this model, so you can check them by yourself if you want....  
message --->(sha256)--->(RSA encryption with private key)--->(base64 encode)---> Signature  
  
To check the signature just check if this two lines are equals  
message --->(sha256)--> hash  
Signature --->(base64 decode)--->(RSA decrypt with public key)---> original_hash  
  
  
## Formatting of messages
Messages with the signature are constructed on the following model:  
> This is the original message  
> The original message can be on multiplte lines  
>   
> Some text to explain how/where you can check the signature  
> SIGNATURE:*(Here goes the signature)*  
  
*Note that a blank line is added after the original message*
